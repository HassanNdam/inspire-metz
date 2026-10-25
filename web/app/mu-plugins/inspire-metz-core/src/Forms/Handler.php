<?php

namespace InspireMetz\Core\Forms;

use WP_Post;

/**
 * Réception des formulaires publics.
 *
 * Les formulaires postent sur admin-post.php (action « im_form »). Le
 * traitement répond en JSON aux requêtes fetch() et redirige sinon, ce qui
 * garde les formulaires utilisables sans JavaScript.
 */
class Handler
{
    protected const MIN_SECONDS = 3;

    protected const RATE_LIMIT = 8;

    public static function boot(): void
    {
        add_action('admin_post_im_form', [self::class, 'handle']);
        add_action('admin_post_nopriv_im_form', [self::class, 'handle']);
    }

    /**
     * Champs cachés à placer dans chaque <form> (action, nonce, anti-spam).
     */
    public static function hidden(string $key): string
    {
        $time = time();

        return implode('', [
            '<input type="hidden" name="action" value="im_form">',
            sprintf('<input type="hidden" name="im_form" value="%s">', esc_attr($key)),
            sprintf('<input type="hidden" name="im_ts" value="%s">', esc_attr($time.'.'.self::sign($time))),
            wp_nonce_field("im_form_{$key}", '_im_nonce', true, false),
            '<div aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden">',
            sprintf('<label>%s <input type="text" name="im_hp" tabindex="-1" autocomplete="off"></label>', esc_html__('Ne pas remplir', 'inspire-metz')),
            '</div>',
        ]);
    }

    /**
     * État d'un formulaire après redirection (succès, erreurs, valeurs saisies).
     *
     * @return array{sent: bool, message: ?string, errors: array<string, string>, old: array<string, mixed>}
     */
    public static function state(string $key): array
    {
        $state = ['sent' => false, 'message' => null, 'errors' => [], 'old' => []];

        if (($_GET['im_sent'] ?? null) === $key) {
            return ['sent' => true, 'message' => Forms::get($key)['success'] ?? null] + $state;
        }

        $token = sanitize_key($_GET['im_token'] ?? '');
        $stored = $token ? get_transient("im_form_state_{$token}") : null;

        if (is_array($stored) && ($stored['form'] ?? null) === $key) {
            return ['errors' => $stored['errors'], 'old' => $stored['old'], 'message' => $stored['message']] + $state;
        }

        return $state;
    }

    public static function handle(): void
    {
        $key = sanitize_key($_POST['im_form'] ?? '');
        $form = Forms::get($key);

        if (! $form) {
            self::respond($key, false, __('Formulaire inconnu.', 'inspire-metz'), [], [], 400);
        }

        if (! wp_verify_nonce($_POST['_im_nonce'] ?? '', "im_form_{$key}")) {
            self::respond($key, false, __('La page a expiré, merci de la recharger et de réessayer.', 'inspire-metz'), [], [], 403);
        }

        // Robots : champ piège rempli ou envoi trop rapide → faux succès silencieux.
        if (! empty($_POST['im_hp']) || ! self::humanTiming($_POST['im_ts'] ?? '')) {
            self::respond($key, true, $form['success']);
        }

        if (self::rateLimited($key)) {
            self::respond($key, false, __('Trop d’envois successifs. Merci de patienter quelques minutes.', 'inspire-metz'), [], [], 429);
        }

        $input = is_array($_POST['im'] ?? null) ? $_POST['im'] : [];
        [$data, $errors] = Validator::validate($form['fields'], $input);

        if ($errors) {
            self::respond($key, false, __('Merci de corriger les champs signalés.', 'inspire-metz'), $errors, $data, 422);
        }

        $post = self::store($key, $form, $data);

        if ($post) {
            self::notify($key, $form, $data, $post);
        }

        self::respond($key, true, $form['success']);
    }

    protected static function store(string $key, array $form, array $data): ?WP_Post
    {
        $existing = null;

        if ($unique = $form['unique'] ?? null) {
            $existing = get_posts([
                'post_type' => $form['post_type'],
                'post_status' => 'any',
                'meta_key' => "im_{$unique}",
                'meta_value' => $data[$unique],
                'numberposts' => 1,
            ])[0] ?? null;
        }

        $id = wp_insert_post([
            'ID' => $existing?->ID ?? 0,
            'post_type' => $form['post_type'],
            'post_status' => 'publish',
            'post_title' => wp_strip_all_tags(($form['title'])($data)),
            // Date UTC explicite : la nuit du changement d'heure, WordPress déduit un
            // décalage erroné de l'heure locale ambiguë et « programme » la réponse.
            'post_date' => current_time('mysql'),
            'post_date_gmt' => current_time('mysql', true),
        ], true);

        if (is_wp_error($id)) {
            return null;
        }

        foreach ($data as $field => $value) {
            update_post_meta($id, "im_{$field}", $value);
        }

        if (! $existing) {
            update_post_meta($id, '_im_state', 'new');
            update_post_meta($id, '_im_source', esc_url_raw(wp_get_referer() ?: ''));
        }

        return get_post($id);
    }

    protected static function notify(string $key, array $form, array $data, WP_Post $post): void
    {
        // « notify » => false : pas de notification, sauf destinataires ajoutés par le filtre.
        $default = ($form['notify'] ?? true) ? [get_option('admin_email')] : [];
        $recipients = apply_filters('inspire_metz/form_recipients', $default, $key);
        $recipients = array_filter(array_map('sanitize_email', (array) $recipients));

        if (! $recipients) {
            return;
        }

        $lines = [];

        foreach ($form['fields'] as $field => [$label]) {
            $lines[] = sprintf('%s : %s', $label, Forms::display($field, $data[$field] ?? ''));
        }

        $lines[] = '';
        $lines[] = sprintf(__('Voir dans l’administration : %s', 'inspire-metz'), admin_url("post.php?post={$post->ID}&action=edit"));

        $headers = [];

        if (! empty($data['email']) && is_email($data['email'])) {
            $headers[] = sprintf('Reply-To: %s', $data['email']);
        }

        wp_mail(
            $recipients,
            sprintf('[%s] %s — %s', get_bloginfo('name'), $form['singular'], $post->post_title),
            implode("\n", $lines),
            $headers
        );
    }

    protected static function respond(string $key, bool $success, ?string $message, array $errors = [], array $old = [], int $status = 200): never
    {
        if (wp_is_json_request() || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
            wp_send_json(['success' => $success, 'message' => $message, 'errors' => $errors], $status);
        }

        $back = remove_query_arg(['im_sent', 'im_token'], wp_get_referer() ?: home_url('/'));

        if ($success) {
            $url = add_query_arg('im_sent', $key, $back);
        } else {
            // Jeton en minuscules : sanitize_key() le relit à l'identique.
            $token = strtolower(wp_generate_password(20, false));
            set_transient("im_form_state_{$token}", ['form' => $key, 'errors' => $errors, 'old' => $old, 'message' => $message], 10 * MINUTE_IN_SECONDS);
            $url = add_query_arg('im_token', $token, $back);
        }

        wp_safe_redirect("{$url}#form-{$key}", 303);
        exit;
    }

    protected static function sign(int $time): string
    {
        return substr(hash_hmac('sha256', (string) $time, wp_salt('nonce')), 0, 16);
    }

    protected static function humanTiming(string $stamp): bool
    {
        [$time, $signature] = array_pad(explode('.', $stamp, 2), 2, '');
        $elapsed = time() - (int) $time;

        return hash_equals(self::sign((int) $time), $signature)
            && $elapsed >= self::MIN_SECONDS
            && $elapsed <= DAY_IN_SECONDS;
    }

    protected static function rateLimited(string $key): bool
    {
        $bucket = 'im_rate_'.md5($key.($_SERVER['REMOTE_ADDR'] ?? ''));
        $count = (int) get_transient($bucket);

        set_transient($bucket, $count + 1, 10 * MINUTE_IN_SECONDS);

        return $count >= self::RATE_LIMIT;
    }
}

<?php

namespace InspireMetz\Core\Forms;

use WP_Post;

/**
 * Consultation des réponses aux formulaires dans l'administration :
 * menu « Formulaires », colonnes, fiche détaillée, suivi et export CSV.
 */
class Admin
{
    protected const MENU = 'edit.php?post_type=im_message';

    public static function states(): array
    {
        return [
            'new' => __('Nouveau', 'inspire-metz'),
            'read' => __('Lu', 'inspire-metz'),
            'done' => __('Traité', 'inspire-metz'),
        ];
    }

    public static function boot(): void
    {
        add_action('init', [self::class, 'register']);
        add_action('admin_menu', [self::class, 'unreadBubble'], 99);
        add_action('add_meta_boxes', [self::class, 'metaBoxes']);
        add_action('save_post', [self::class, 'saveState'], 10, 2);
        add_action('admin_post_im_export', [self::class, 'export']);

        foreach (Forms::all() as $key => $form) {
            $type = $form['post_type'];

            add_filter("manage_{$type}_posts_columns", fn ($columns) => self::columns($form, $columns));
            add_action("manage_{$type}_posts_custom_column", [self::class, 'column'], 10, 2);
            add_filter("views_edit-{$type}", fn ($views) => $views + ['im_export' => self::exportLink($key)]);
        }
    }

    public static function register(): void
    {
        $first = true;

        foreach (Forms::all() as $form) {
            register_post_type($form['post_type'], [
                'labels' => [
                    'name' => $form['label'],
                    'singular_name' => $form['singular'],
                    'menu_name' => __('Formulaires', 'inspire-metz'),
                    'all_items' => $form['label'],
                    'edit_item' => $form['singular'],
                    'search_items' => __('Rechercher', 'inspire-metz'),
                    'not_found' => __('Aucune réponse pour le moment.', 'inspire-metz'),
                ],
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => $first ? true : self::MENU,
                'menu_icon' => 'dashicons-email-alt',
                'menu_position' => 26,
                'supports' => false,
                'capability_type' => 'post',
                'capabilities' => ['create_posts' => 'do_not_allow'],
                'map_meta_cap' => true,
            ]);

            $first = false;
        }
    }

    /**
     * Pastille du nombre de messages non lus, comme pour les commentaires.
     */
    public static function unreadBubble(): void
    {
        global $menu, $submenu;

        $total = 0;

        foreach (Forms::all() as $form) {
            $count = self::unreadCount($form['post_type']);
            $total += $count;

            foreach ($submenu[self::MENU] ?? [] as $index => $item) {
                if ($count && $item[2] === "edit.php?post_type={$form['post_type']}") {
                    $submenu[self::MENU][$index][0] .= self::bubble($count);
                }
            }
        }

        foreach ($menu as $index => $item) {
            if ($total && ($item[2] ?? null) === self::MENU) {
                $menu[$index][0] .= self::bubble($total);
            }
        }
    }

    protected static function unreadCount(string $postType): int
    {
        return count(get_posts([
            'post_type' => $postType,
            'post_status' => 'publish',
            'meta_key' => '_im_state',
            'meta_value' => 'new',
            'fields' => 'ids',
            'numberposts' => 100,
        ]));
    }

    protected static function bubble(int $count): string
    {
        return sprintf(' <span class="awaiting-mod count-%1$d"><span class="pending-count">%1$d</span></span>', $count);
    }

    protected static function columns(array $form, array $columns): array
    {
        $fields = [];

        foreach ($form['columns'] as $field) {
            $fields["im_{$field}"] = $form['fields'][$field][0];
        }

        return ['cb' => $columns['cb']] + $fields + [
            'im_state' => __('Suivi', 'inspire-metz'),
            'date' => __('Reçu le', 'inspire-metz'),
        ];
    }

    public static function column(string $column, int $postId): void
    {
        if ($column === 'im_state') {
            $state = get_post_meta($postId, '_im_state', true) ?: 'read';
            printf('<span class="im-state im-state-%s">%s</span>', esc_attr($state), esc_html(self::states()[$state] ?? $state));

            return;
        }

        if (! str_starts_with($column, 'im_')) {
            return;
        }

        $field = substr($column, 3);
        $value = Forms::display($field, get_post_meta($postId, $column, true));
        $isFirst = $field === (Forms::byPostType(get_post_type($postId))['columns'][0] ?? null);

        if ($isFirst) {
            printf('<strong><a class="row-title" href="%s">%s</a></strong>', esc_url(get_edit_post_link($postId)), esc_html($value ?: __('(sans nom)', 'inspire-metz')));

            return;
        }

        echo $field === 'email' && is_email($value)
            ? sprintf('<a href="mailto:%1$s">%1$s</a>', esc_html($value))
            : esc_html($value);
    }

    public static function metaBoxes(string $postType): void
    {
        $form = Forms::byPostType($postType);

        if (! $form) {
            return;
        }

        add_meta_box('im_details', $form['singular'], [self::class, 'detailsBox'], $postType, 'normal', 'high');
        add_meta_box('im_state', __('Suivi', 'inspire-metz'), [self::class, 'stateBox'], $postType, 'side', 'high');

        // Ouvrir une réponse « Nouveau » la passe en « Lu ».
        if (($post = get_post()) && get_post_meta($post->ID, '_im_state', true) === 'new') {
            update_post_meta($post->ID, '_im_state', 'read');
        }
    }

    public static function detailsBox(WP_Post $post): void
    {
        $form = Forms::byPostType($post->post_type);

        echo '<table class="widefat striped"><tbody>';

        foreach ($form['fields'] as $field => [$label]) {
            $value = Forms::display($field, get_post_meta($post->ID, "im_{$field}", true));

            printf(
                '<tr><th style="width:30%%">%s</th><td>%s</td></tr>',
                esc_html($label),
                $field === 'message' ? nl2br(esc_html($value)) : esc_html($value)
            );
        }

        printf(
            '<tr><th>%s</th><td>%s</td></tr>',
            esc_html__('Envoyé depuis', 'inspire-metz'),
            esc_html(get_post_meta($post->ID, '_im_source', true) ?: '—')
        );

        echo '</tbody></table>';

        if ($email = get_post_meta($post->ID, 'im_email', true)) {
            printf('<p><a class="button" href="mailto:%1$s">%2$s</a></p>', esc_attr($email), esc_html__('Répondre par email', 'inspire-metz'));
        }
    }

    public static function stateBox(WP_Post $post): void
    {
        $current = get_post_meta($post->ID, '_im_state', true) ?: 'read';

        wp_nonce_field('im_state', '_im_state_nonce');

        echo '<select name="im_state" style="width:100%">';

        foreach (self::states() as $value => $label) {
            printf('<option value="%s" %s>%s</option>', esc_attr($value), selected($current, $value, false), esc_html($label));
        }

        echo '</select>';
    }

    public static function saveState(int $postId, WP_Post $post): void
    {
        if (! Forms::byPostType($post->post_type) || ! isset($_POST['im_state'])) {
            return;
        }

        if (! wp_verify_nonce($_POST['_im_state_nonce'] ?? '', 'im_state') || ! current_user_can('edit_post', $postId)) {
            return;
        }

        $state = sanitize_key($_POST['im_state']);

        if (array_key_exists($state, self::states())) {
            update_post_meta($postId, '_im_state', $state);
        }
    }

    protected static function exportLink(string $key): string
    {
        $url = wp_nonce_url(admin_url("admin-post.php?action=im_export&form={$key}"), "im_export_{$key}");

        return sprintf('<a href="%s">%s</a>', esc_url($url), esc_html__('Exporter en CSV', 'inspire-metz'));
    }

    /**
     * Export CSV compatible Excel (UTF-8 avec BOM, séparateur « ; »).
     */
    public static function export(): void
    {
        $key = sanitize_key($_GET['form'] ?? '');
        $form = Forms::get($key);

        if (! $form || ! current_user_can('edit_posts') || ! wp_verify_nonce($_GET['_wpnonce'] ?? '', "im_export_{$key}")) {
            wp_die(esc_html__('Export non autorisé.', 'inspire-metz'), 403);
        }

        $posts = get_posts(['post_type' => $form['post_type'], 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC']);

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header(sprintf('Content-Disposition: attachment; filename="inspire-metz-%s-%s.csv"', $key, wp_date('Y-m-d')));

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, array_merge([__('Date', 'inspire-metz')], array_column($form['fields'], 0), [__('Suivi', 'inspire-metz')]), ';');

        foreach ($posts as $post) {
            $row = [get_the_date('d/m/Y H:i', $post)];

            foreach (array_keys($form['fields']) as $field) {
                $row[] = Forms::display($field, get_post_meta($post->ID, "im_{$field}", true));
            }

            $row[] = self::states()[get_post_meta($post->ID, '_im_state', true)] ?? '';

            fputcsv($out, $row, ';');
        }

        fclose($out);
        exit;
    }
}

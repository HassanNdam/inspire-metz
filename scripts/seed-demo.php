<?php

/**
 * Contenu de démonstration Inspire Metz.
 *
 *   php vendor/wp-cli/wp-cli/php/boot-fs.php eval-file scripts/seed-demo.php
 *
 * Idempotent : pages, contenus et médias sont retrouvés par leur slug ou leur
 * clé d'import, puis mis à jour. Les photos sont téléchargées depuis le site
 * en ligne inspire-metz.com (propriété de l'agence, à valider avant toute mise
 * en production) ; deux visuels issus de la maquette sont dans scripts/demo-media.
 */

defined('ABSPATH') || exit('Lancer avec wp eval-file.');

require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/media.php';
require_once ABSPATH.'wp-admin/includes/image.php';

const IM_UPLOADS = 'https://inspire-metz.com/app/uploads/';
const IM_THEME_IMAGES = 'https://inspire-metz.com/app/themes/inspire-portail/public/images/';

$live = $GLOBALS['live'] = json_decode(file_get_contents(__DIR__.'/demo-content/live.json'), true);

/*
|--------------------------------------------------------------------------
| Outils
|--------------------------------------------------------------------------
*/

function im_log(string $message): void
{
    WP_CLI::log($message);
}

/**
 * Importe un média une seule fois (clé = nom du fichier) et renvoie son ID.
 */
function im_media(string $source, string $alt = '', string $caption = ''): int
{
    static $cache = [];
    // Un fragment (#nom.jpg) donne un nom unique aux fichiers homonymes (vignettes YouTube).
    $key = parse_url($source, PHP_URL_FRAGMENT) ?: basename(parse_url($source, PHP_URL_PATH));

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    $existing = get_posts(['post_type' => 'attachment', 'post_status' => 'any', 'meta_key' => '_im_seed_key', 'meta_value' => $key, 'fields' => 'ids', 'numberposts' => 1]);

    if ($existing) {
        return $cache[$key] = $existing[0];
    }

    if (str_starts_with($source, 'http')) {
        $tmp = download_url($source, 60);
    } else {
        $tmp = wp_tempnam($key);
        copy($source, $tmp);
    }

    if (is_wp_error($tmp)) {
        WP_CLI::warning("Média introuvable : {$source} ({$tmp->get_error_message()})");

        return $cache[$key] = 0;
    }

    $id = media_handle_sideload(['name' => $key, 'tmp_name' => $tmp], 0, $alt ?: null, ['post_excerpt' => $caption]);

    if (is_wp_error($id)) {
        @unlink($tmp);
        WP_CLI::warning("Import impossible : {$key} ({$id->get_error_message()})");

        return $cache[$key] = 0;
    }

    update_post_meta($id, '_im_seed_key', $key);
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    im_log("  média : {$key}");

    return $cache[$key] = $id;
}

function im_upload(string $path, string $alt = '', string $caption = ''): int
{
    return im_media(IM_UPLOADS.$path, $alt, $caption);
}

/**
 * Crée ou met à jour un contenu identifié par son type et son slug.
 */
function im_post(string $type, string $slug, string $title, array $args = []): int
{
    $parent = $args['post_parent'] ?? 0;
    $existing = get_posts(['post_type' => $type, 'name' => $slug, 'post_parent' => $type === 'page' ? $parent : '', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids']);

    // Date UTC explicite : sinon, la nuit du changement d'heure, WordPress peut
    // calculer une date GMT dans le futur et passer le contenu en « programmé ».
    $current = $existing ? get_post($existing[0]) : null;
    $dates = match (true) {
        isset($args['post_date']) => ['post_date_gmt' => get_gmt_from_date($args['post_date'])],
        (bool) $current => ['post_date' => $current->post_date, 'post_date_gmt' => min($current->post_date_gmt, current_time('mysql', true))],
        default => ['post_date' => current_time('mysql'), 'post_date_gmt' => current_time('mysql', true)],
    };

    $id = wp_insert_post([
        'ID' => $existing[0] ?? 0,
        'post_type' => $type,
        'post_name' => $slug,
        'post_title' => $title,
        'post_status' => 'publish',
        'post_content' => '',
    ] + $args + $dates, true);

    if (is_wp_error($id)) {
        WP_CLI::error("{$type} {$slug} : {$id->get_error_message()}");
    }

    return $id;
}

/**
 * Enregistre des champs SCF par nom (résolus en clés via le groupe local).
 */
function im_fields(int|string $target, array $values): void
{
    foreach ($values as $name => $value) {
        $field = acf_maybe_get_field($name, $target, false);
        $key = $field['key'] ?? im_field_key($name);

        if (! $key) {
            WP_CLI::warning("Champ inconnu : {$name}");

            continue;
        }

        update_field($key, $value, $target);
    }
}

function im_field_key(string $name): ?string
{
    static $map;

    if ($map === null) {
        $map = [];

        foreach (acf_get_field_groups() as $group) {
            foreach (acf_get_fields($group) as $field) {
                $map[$field['name']] ??= $field['key'];
            }
        }
    }

    return $map[$name] ?? null;
}

function im_link(string $url, string $title, bool $external = false): array
{
    return ['url' => $url, 'title' => $title, 'target' => $external ? '_blank' : ''];
}

/**
 * Paragraphe complet du site en ligne, retrouvé par son début.
 */
function im_live(string $page, string $startsWith): string
{
    $live = $GLOBALS['live'];

    foreach ($live[$page] ?? [] as [$tag, $text]) {
        if (str_starts_with($text, $startsWith)) {
            return $text;
        }
    }

    WP_CLI::warning("Texte introuvable ({$page}) : {$startsWith}");

    return $startsWith;
}

/**
 * Convertit le contenu d'un article du site en ligne en HTML (h2, p, listes).
 */
function im_live_html(string $page, int $skip = 0, ?int $limit = null): string
{
    $live = $GLOBALS['live'];

    $rows = array_slice(array_values(array_filter($live[$page] ?? [], fn ($row) => $row[0] !== 'h1' && $row[0] !== 'figcaption')), $skip, $limit);
    $html = '';
    $inList = false;

    foreach ($rows as [$tag, $text]) {
        if ($tag === 'li' && ! $inList) {
            $html .= '<ul>';
            $inList = true;
        }

        if ($tag !== 'li' && $inList) {
            $html .= '</ul>';
            $inList = false;
        }

        $html .= sprintf('<%1$s>%2$s</%1$s>', $tag === 'h3' ? 'h3' : $tag, esc_html($text));
    }

    return $html.($inList ? '</ul>' : '');
}

function im_block(string $layout, array $values, string $background = 'clair'): array
{
    return ['acf_fc_layout' => $layout, 'fond' => $background] + $values;
}

/*
|--------------------------------------------------------------------------
| Médias
|--------------------------------------------------------------------------
*/

im_log('Médias…');

$img = [
    'aerien' => im_upload('2025/03/vue-aerienne-metz-centre-cathedrale.jpg', 'Vue aérienne de Metz et de la cathédrale Saint-Étienne'),
    'territoire' => im_media(IM_THEME_IMAGES.'animation/image-right-2.c0fabb.jpg', 'Metz vue du ciel, la Moselle et la cathédrale'),
    'avenir' => im_upload('2025/03/bandeau-avenir-durable-1.jpg', 'Vue sur Metz et le plan d’eau', '© Philippe Gisselbrecht - Ville de Metz'),
    'saulny' => im_upload('2025/03/photo-saulny.jpg', 'Paysage de Saulny'),
    'fluvial' => im_upload('2025/03/espace-fluvial_inspire-metz-min.jpeg', 'Le plan d’eau de Metz', '© Sutter Event'),
    'mcrs' => im_upload('2026/02/mcrs-accueil.jpg', 'Metz Congrès Robert Schuman', '© Luc Boegly'),
    'photonique' => im_upload('2026/02/photonique-accueil.jpg', 'Laboratoire de photonique', '© Pixabay'),
    'office' => im_upload('2025/03/office-tourisme-metz-min.jpeg', 'L’office de tourisme de Metz, place d’Armes'),
    'contact' => im_upload('2025/02/image-contact.png', 'Façade de l’office de tourisme de Metz'),
    'missions' => im_upload('2025/03/projets-et-missions-inspire-metz.jpg', 'The MIX', '© Inspire Metz'),
    'etudiants' => im_upload('2025/02/groupe-etudiants.jpeg', 'Groupe d’étudiants à Metz'),
    'roue' => im_upload('2025/11/grande-roue.jpg', 'Grande roue du marché de Noël, place d’Armes'),
    'noel' => im_upload('2025/11/marche-noel-saint-louis.jpg', 'Marché de Noël place Saint-Louis', '© Philippe Gisselbrecht – Ville de Metz / Benoît Lapray'),
    'emm_blanc' => im_upload('2026/05/ancien-logo-emm-blanc.png', 'Euro-Métropole de Metz'),
    'grand_est' => im_upload('2025/03/grand-est-logo.png', 'Région Grand Est'),
    'emm_couleur' => im_upload('2026/05/euro-metropole-2026-2.jpg', 'Euro-Métropole de Metz'),
    'grand_est_couleur' => im_upload('2025/03/logo-grand-est.jpg', 'Région Grand Est'),
    'shard_mcrs' => im_upload('2026/02/mcrs-shard.jpg', 'Metz Congrès Robert Schuman'),
    'shard_eco' => im_upload('2025/03/visuel_eco.jpg', 'Laboratoire de photonique'),
    'video_home' => im_media(IM_THEME_IMAGES.'animation/home-video.3c96e7.mp4#home-video.mp4', 'Vue aérienne de Metz'),
    'icon_gauche' => im_upload('2025/03/Icon-gauche.png', ''),
    'icon_droite' => im_upload('2025/03/Icon-droite.png', ''),
    'amb1' => im_upload('2025/05/ambassadeur01.jpg', 'Ambassadeurs de l’Euro-Métropole de Metz', '© Inspire Metz'),
    'amb2' => im_upload('2025/05/ambassadeur02.jpg', 'Ambassadeur de l’Euro-Métropole de Metz', '© Inspire Metz'),
    'amb3' => im_upload('2025/05/ambassadeur03.jpg', 'Ambassadrice de l’Euro-Métropole de Metz', '© Inspire Metz'),
    'amb4' => im_upload('2025/05/ambassadeur04.jpg', 'Ambassadeur de l’Euro-Métropole de Metz', '© Inspire Metz'),
    'ra_vignette' => im_upload('2026/04/miniature-ra-2025.jpg', 'Couverture du rapport d’activité 2025'),
    'ra_pdf' => im_upload('2026/04/RA-2025-inspire-metz.pdf'),
    'plan' => im_media(__DIR__.'/demo-media/plan-acces-metz.jpg', 'Plan d’accès : l’agence Inspire Metz au centre de Metz'),
    'apercu' => im_media(__DIR__.'/demo-media/apercu-newsletter.jpg', 'Aperçu de la lettre d’information Inspire Metz'),
];

/*
|--------------------------------------------------------------------------
| Communes, vidéos, Jeem, communiqués, newsletters
|--------------------------------------------------------------------------
*/

im_log('Communes…');

$communes = ['Amanvillers', 'Ars-Laquenexy', 'Ars-sur-Moselle', 'Augny', 'Châtel-Saint-Germain', 'Chesny', 'Chieulles', 'Coin-lès-Cuvry', 'Coin-sur-Seille', 'Cuvry', 'Féy', 'Gravelotte', 'Jury', 'Jussy', 'Laquenexy', 'La Maxe', 'Le Ban-Saint-Martin', 'Lessy', 'Longeville-lès-Metz', 'Lorry-lès-Metz', 'Lorry-Mardigny', 'Marieulles-Vezon', 'Marly', 'Mécleuves', 'Metz', 'Mey', 'Montigny-lès-Metz', 'Moulins-lès-Metz', 'Noisseville', 'Nouilly', 'Peltre', 'Plappeville', 'Pouilly', 'Pournoy-la-Chétive', 'Roncourt', 'Rozérieulles', 'Saint-Julien-lès-Metz', 'Saint-Privat-la-Montagne', 'Sainte-Ruffine', 'Saulny', 'Scy-Chazelles', 'Vantoux', 'Vany', 'Vaux', 'Vernéville', 'Woippy'];

foreach ($communes as $order => $name) {
    im_post('commune', sanitize_title($name), $name, ['menu_order' => $order]);
}

im_log('Vidéos…');

$videos = [
    ['GDNlsrFAnxE', 'Cet été, (re)découvrez l’Euro-Métropole de Metz', 'Une destination accueillante, musicale, joueuse, sportive, aux couleurs du soleil et baignée des lumières de la Moselle, grâce à la complicité de ses habitants, sportifs et artistes.'],
    ['ZtAh-iQigmY', 'Metz c’est mieux, la Moselle c’est mieux !', 'L’Euro-Métropole de Metz et le Département de la Moselle s’associent pour communiquer auprès des futurs étudiants et mettre en avant les atouts du territoire pour y poursuivre leurs études supérieures.'],
    ['A54E3ZZegWw', 'Euro-Métropole de Metz, destination européenne créative & durable', 'Le film de promotion du territoire de l’Euro-Métropole de Metz. Une production Fensch Toast sur la musique de Chapelier fou.'],
];

foreach ($videos as $order => [$youtube, $title, $excerpt]) {
    $id = im_post('video', sanitize_title($title), $title, ['post_excerpt' => $excerpt, 'menu_order' => $order]);
    im_fields($id, ['video' => "https://www.youtube.com/watch?v={$youtube}"]);
    set_post_thumbnail($id, im_media("https://img.youtube.com/vi/{$youtube}/maxresdefault.jpg#{$youtube}.jpg", $title));
}

im_log('Jeem…');

$jeems = [
    ['23', 'Octobre 2026', '2026/10/couv-jeem-23.jpg', '2026-10-06', 'https://inspire-metz.com/jeem-n23-octobre-2026-la-ou-leconomie-prend-vie/'],
    ['22', 'Juin 2026', '2026/06/couverture-jeem-22.jpg', '2026-06-19', 'https://inspire-metz.com/jeem-n22-juin-2026-vos-prochains-rendez-vous/'],
    ['21', 'Février 2026', '2026/02/jeem21-couv.jpg', '2026-02-09', 'https://inspire-metz.com/jeem-n21-fevrier-2026-les-entreprises-au-coeur-du-territoire/'],
    ['20', 'Octobre 2025', '2025/11/jeem-couv-20.jpg', '2025-11-03', 'https://inspire-metz.com/jeem-20-cap-sur-des-bonnes-nouvelles/'],
    ['19', 'Juillet 2025', '2025/07/jeem19.jpg', '2025-07-02', 'https://inspire-metz.com/sortie-du-magazine-economique-jeem-n19-de-juillet-2025/'],
];

foreach ($jeems as [$numero, $periode, $cover, $date, $url]) {
    $id = im_post('jeem', "jeem-{$numero}", "Jeem n°{$numero}", ['post_date' => "{$date} 09:00:00"]);
    im_fields($id, ['numero' => $numero, 'periode' => $periode, 'lien' => $url]);
    set_post_thumbnail($id, im_upload($cover, "Couverture du Jeem n°{$numero}"));
}

im_log('Communiqués…');

$communiques = [
    ['Les nouveautés de l’Agence Inspire Metz - Office de Tourisme : animations et nouveaux produits de la boutique', '2026-04-14', '2026/04/cp-produits-boutique.jpg', '2026/04/CP-produits-boutique2026-InspireMetz.pdf'],
    ['Rapport d’activité 2025 de l’agence Inspire Metz', '2026-04-02', null, null],
    ['Bilan barométrique du Centre Pompidou-Metz 2024', '2025-07-24', '2025/07/Capture-decran-2025-07-24-a-11.16.15.jpeg', '2025/07/CP-BILAN-BAROMETRE.pdf'],
    ['Concours « Best Christmas Markets » : le Marché de Noël à Metz à l’honneur', '2024-12-18', '2025/03/com-presse-37.jpg', null],
    ['Marché de Noël à Metz : lancement de la saison 2024', '2024-11-17', '2025/03/com-presse-36.jpg', null],
    ['Les agences de développement économique et d’attractivité du Grand Est vous accompagnent', '2024-09-20', '2025/03/com-presse-26.jpg', null],
];

foreach ($communiques as [$title, $date, $cover, $pdf]) {
    $id = im_post('communique', sanitize_title($title), $title, ['post_date' => "{$date} 09:00:00"]);

    $file = $pdf ? im_upload($pdf) : ($cover ? 0 : $img['ra_pdf']);
    im_fields($id, ['fichier' => $file ?: '', 'lien' => $file ? '' : 'https://inspire-metz.com/communiques-de-presse/']);
    set_post_thumbnail($id, $cover ? im_upload($cover, $title) : $img['ra_vignette']);
}

im_log('Newsletters envoyées…');

$numero = 162;
$date = new DateTimeImmutable('2026-10-20');

foreach ($live['newsletters'] as [$tag, $text]) {
    if ($tag !== 'p' || ! str_contains($text, ' / ')) {
        continue;
    }

    $id = im_post('lettre_info', "newsletter-{$numero}", "Newsletter n°{$numero}", [
        'post_excerpt' => $text,
        'post_date' => $date->format('Y-m-d 08:00:00'),
    ]);
    im_fields($id, ['lien' => 'https://inspire-metz.com/newsletters/']);

    $numero--;
    $date = $date->modify('-14 days');
}

/*
|--------------------------------------------------------------------------
| Actualités
|--------------------------------------------------------------------------
*/

im_log('Actualités…');

$articles = [
    ['jeem n°23 – octobre 2026 : là où l’économie prend vie', '2026-10-06', '2026/10/jeem23.jpg', true, 'jeem-n23-octobre-2026-la-ou-leconomie-prend-vie', 10],
    ['jeem n°22 – juin 2026 : vos prochains rendez-vous', '2026-06-19', '2026/06/couverture-jeem-22.jpg', true, null, null],
    ['jeem n°21 – février 2026 : les entreprises au cœur du territoire', '2026-02-09', '2026/02/jeem21.jpg', false, null, null],
    ['Noël à Metz, marché merveilleux', '2025-11-03', '2025/11/grande-roue.jpg', false, 'noel-a-metz-marche-merveilleux', null],
    ['JEEM #20 : cap sur des bonnes nouvelles !', '2025-11-03', '2025/11/jeem20-mise-en-avant.jpg', false, null, null],
    ['Sortie du magazine économique jeem n°19 de juillet 2025', '2025-07-02', '2025/07/jeem-19-paysage.jpg', false, null, null],
    ['Dimanche sans fin à Metz et dans l’Euro-Métropole', '2025-04-29', '2025/04/dimanche-sans-fin-2025-paysage.jpg', false, 'dimanche-sans-fin-a-metz-et-dans-leurometropole', null],
    ['Nouvelle édition de jeem n°18 / Mars 2025', '2025-03-13', '2025/03/jeem-18_article.webp', false, null, null],
    ['L’hôtel Maison Heler, imaginé par Philippe Starck : ouverture en mars 2025', '2025-02-19', '2024/12/960_400_7_maison-heler-2.webp', false, null, null],
];

$generic = '<p>Le magazine économique de l’Euro-Métropole de Metz donne la parole à celles et ceux qui font vivre le territoire : entreprises, commerces, porteurs de projets et acteurs de l’innovation.</p><p>Retrouvez dans ce numéro les portraits, les chiffres clés et les rendez-vous qui rythment la vie économique métropolitaine.</p>';

foreach ($articles as [$title, $date, $image, $featured, $livePage, $limit]) {
    $id = im_post('post', sanitize_title($title), $title, [
        'post_date' => "{$date} 10:00:00",
        'post_excerpt' => wp_trim_words(wp_strip_all_tags($livePage ? im_live_html($livePage, 1, 2) : $generic), 30),
    ]);

    set_post_thumbnail($id, im_upload($image, $title));

    $blocks = [im_block('texte', ['texte' => $livePage ? im_live_html($livePage, 0, $limit) : $generic])];

    if ($livePage === 'noel-a-metz-marche-merveilleux') {
        $blocks[] = im_block('texte_image', ['titre' => 'Une ville en lumières', 'texte' => '<p>Metz, ville d’eau et de lumière, accueille le Sentier des Lanternes au square Boufflers et prolonge la magie de Noël dans les communes de l’Euro-Métropole.</p>', 'image' => $img['noel'], 'position' => 'droite', 'lien' => im_link('https://tourisme-metz.com/', 'Préparer sa visite', true)]);
    }

    im_fields($id, ['blocs' => $blocks, 'a_la_une' => $featured]);
}

/*
|--------------------------------------------------------------------------
| Pages
|--------------------------------------------------------------------------
*/

im_log('Pages…');

$home = im_post('page', 'accueil', 'Accueil');
$news = im_post('page', 'actualites', 'Actualités');
$contact = im_post('page', 'contact', 'Contact', ['page_template' => 'template-contact.blade.php']);
$presse = im_post('page', 'espace-presse', 'Espace presse', ['page_template' => 'template-presse.blade.php']);
$newsletters = im_post('page', 'newsletters', 'Newsletters', ['page_template' => 'template-newsletters.blade.php']);

$decouvrir = im_post('page', 'decouvrir-leurometropole-de-metz', 'Découvrir l’Euro-Métropole de Metz');
$communesPage = im_post('page', '46-communes', 'Les 46 communes', ['post_parent' => $decouvrir]);
$durable = im_post('page', 'destination-durable', 'Destination durable', ['post_parent' => $decouvrir]);
$videosPage = im_post('page', 'videos', 'Vidéos', ['post_parent' => $decouvrir, 'page_template' => 'template-videos.blade.php']);

$installer = im_post('page', 'sinstaller-sur-le-territoire', 'S’installer sur le territoire');
$raisons = im_post('page', '10-raisons-de-sinstaller-dans-leurometropole-de-metz', '10 raisons de s’installer dans l’Euro-Métropole de Metz', ['post_parent' => $installer]);
$guide = im_post('page', 'guide-daccueil-des-nouveaux-arrivants', 'Guide d’accueil des nouveaux arrivants', ['post_parent' => $installer]);
$ambassadeur = im_post('page', 'ambassadeur', 'Ambassadeur de l’Euro-Métropole de Metz', ['post_parent' => $installer]);

$agence = im_post('page', 'lagence-inspire-metz', 'L’agence Inspire Metz');
$presentation = im_post('page', 'presentation-et-missions', 'Présentation et missions', ['post_parent' => $agence]);

$mentions = im_post('page', 'mentions-legales', 'Mentions légales');
$cookies = im_post('page', 'cookies', 'Cookies');
$privacy = (int) get_option('wp_page_for_privacy_policy') ?: im_post('page', 'confidentialite', 'Confidentialité');
wp_update_post(['ID' => $privacy, 'post_title' => 'Confidentialité', 'post_name' => 'confidentialite', 'post_status' => 'publish', 'post_content' => '']);

update_option('show_on_front', 'page');
update_option('page_on_front', $home);
update_option('page_for_posts', $news);
update_option('wp_page_for_privacy_policy', $privacy);
update_option('posts_per_page', 9);

// Accueil
im_fields($home, [
    'hero_titre' => "Découvrez\nl’Euro-Métropole de Metz,",
    'hero_titre_accent' => "territoire innovant\n& durable",
    'hero_images' => [$img['shard_mcrs'], $img['shard_eco'], $img['fluvial']],
    'territoire_titre' => 'Un territoire au centre des échanges transfrontaliers et internationaux',
    'territoire_texte' => '<p>'.im_live('inspire-metz.com', 'Située à proximité').'</p><p>'.im_live('inspire-metz.com', 'À 85 minutes de Paris, 50').'</p>',
    'territoire_lien' => im_link(get_permalink($communesPage), 'Découvrir le territoire'),
    'territoire_video' => $img['video_home'],
    'territoire_image' => $img['territoire'],
    'territoire_video_image' => $img['office'],
    'territoire_video_lien' => im_link(get_permalink($videosPage), 'Vidéos'),
    'agence_titre' => 'L’agence d’attractivité Inspire Metz',
    'agence_texte' => '<p>'.im_live('inspire-metz.com', 'Au service des 46 communes').'</p><p>L’agence est également chargée du renforcement de la notoriété de la destination.</p>',
    'agence_lien' => im_link(get_permalink($presentation), 'Découvrir l’agence'),
    'agence_image' => $img['office'],
    'agence_soutiens' => '<p>Inspire Metz est l’agence d’attractivité au service des 46 communes de l’Euro-Métropole de Metz, <strong>également soutenue par la Région Grand Est sur le volet Attractivité économique.</strong></p>',
    'agence_logos' => [
        ['logo' => $img['emm_blanc'], 'url' => 'https://www.eurometropolemetz.eu/'],
        ['logo' => $img['grand_est'], 'url' => 'https://www.grandest.fr/'],
    ],
    'actualites_titre' => 'Nos actualités',
    'actualites_nombre' => 6,
    'presse_titre' => 'Retrouvez notre magazine Jeem et nos communiqués dans l’espace Presse',
    'presse_lien' => im_link(get_permalink($presse), 'Consulter'),
    'missions_titre' => 'Nos missions',
    'reseaux_titre' => 'Suivez-nous sur les réseaux',
    'reseaux_images' => [$img['roue'], $img['noel'], $img['saulny'], $img['fluvial'], $img['etudiants'], $img['missions']],
]);

// Actualités
im_fields($news, [
    'chapo' => 'Retrouvez toute l’actualité de l’agence d’attractivité Inspire Metz.',
    'une_titre' => 'À la une',
    'liste_titre' => 'Toutes les actualités',
]);

// Contact
im_fields($contact, [
    'chapo' => 'Vous souhaitez contacter l’Agence Inspire Metz – Office de Tourisme ? Envoyez un message ou appelez-nous pour démarrer une conversation.',
    'carte' => $img['plan'],
    'photo' => $img['contact'],
]);

// Espace presse
im_fields($presse, [
    'chapo' => 'Retrouvez ci-dessous les derniers Jeem ainsi que tous les communiqués de presse de l’agence Inspire Metz.',
    'jeem_titre' => 'Consultez les derniers Jeem',
    'jeem_lien' => im_link('https://inspire-metz.com/actualites/', 'Voir les anciens numéros', true),
    'promo_titre' => 'Jeem revient avec un nouveau format',
    'promo_texte' => im_live('communiques-de-presse', 'Magazine économique'),
    'promo_image' => im_upload('2025/11/jeem20-mise-en-avant.jpg', 'Jeem, le journal économique de l’Euro-Métropole de Metz'),
    'liste_titre' => 'Retrouvez tous les communiqués de presse de l’agence Inspire Metz',
]);

// Newsletters
im_fields($newsletters, [
    'chapo' => 'Restez informé sur l’actualité du territoire de l’Euro-Métropole de Metz grâce à nos newsletters.',
    'inscription_titre' => 'S’inscrire à la newsletter',
    'inscription_texte' => 'Vous recevrez deux emails par mois pour vous communiquer les actualités économiques et touristiques de l’agence Inspire Metz.',
    'inscription_apercu' => $img['apercu'],
    'liste_titre' => 'Retrouvez ici les newsletters de l’agence Inspire Metz',
]);

// Découvrir
im_fields($decouvrir, [
    'chapo' => 'Située au cœur de l’Europe, à proximité de trois frontières, l’Euro-Métropole de Metz réunit 46 communes autour d’une même ambition : un territoire innovant, durable et accueillant.',
    'blocs' => [im_block('cartes', ['cartes' => [
        ['titre' => 'Un territoire avec 46 communes', 'lien' => im_link(get_permalink($communesPage), 'Un territoire avec 46 communes'), 'image' => $img['avenir']],
        ['titre' => 'Destination durable', 'lien' => im_link(get_permalink($durable), 'Destination durable'), 'image' => $img['saulny']],
        ['titre' => 'Vidéos', 'lien' => im_link(get_permalink($videosPage), 'Vidéos'), 'image' => $img['noel']],
    ]], 'sombre')],
]);

// Structure du site en ligne : page purement éditoriale sous le bandeau.
im_fields($communesPage, [
    'chapo' => '',
    'bandeau' => $img['avenir'],
    'blocs' => [
        im_block('texte', ['texte' => implode('', [
            '<p>'.im_live('46-communes', 'Sa situation géographique').'</p>',
            '<h2>Un territoire durable</h2><p>'.im_live('46-communes', 'Labellisée Destination').'</p>',
            '<h2>Une destination créative &amp; innovante</h2><p>'.im_live('46-communes', '…quel que soit').'</p>',
            '<h2>Une Euro-Métropole attractive</h2><p>'.im_live('46-communes', '…pour les talents').'</p>',
            '<h2>Un territoire à investir</h2><p>'.im_live('46-communes', 'Avec 27 zones').'</p>',
            '<h2>Aux sources du commerce</h2><p>'.im_live('46-communes', 'Forte d’une zone').'</p>',
            '<h2>Destination touristique et territoire de lumières</h2><p>'.im_live('46-communes', 'Forte de 3 000 ans').'</p>',
            '<p>'.im_live('46-communes', 'Ville d’eau et ville-jardins').'</p>',
            '<p>'.im_live('46-communes', 'Les 46 communes de l’Euro-Métropole proposent').'</p>',
            '<p>'.im_live('46-communes', 'Quelle que soit la commune').'</p>',
            '<p>Bienvenue dans l’Euro-Métropole de Metz !</p>',
        ])]),
    ],
]);

im_fields($durable, [
    'chapo' => 'Découvrez comment l’Euro-Métropole de Metz s’engage pour un avenir durable.',
    'bandeau' => $img['saulny'],
    'blocs' => [
        im_block('texte', ['texte' => '<h2>Le développement durable dans l’ADN du territoire</h2><p>'.im_live('destination-durable', 'Le développement durable').'</p><p>'.im_live('destination-durable', 'Cet engagement').'</p>']),
        im_block('chiffres', ['chiffres' => [
            ['valeur' => '1970', 'libelle' => 'Naissance de l’écologie urbaine avec Jean-Marie Pelt'],
            ['valeur' => '2019', 'libelle' => 'G7 de l’Environnement et des Océans à Metz'],
            ['valeur' => '2022', 'libelle' => 'Certification ISO 20121 et label Destination Innovante Durable'],
        ]]),
        im_block('texte', ['texte' => '<p>'.im_live('destination-durable', 'Suite à cet évènement').'</p><p>'.im_live('destination-durable', 'Enfin le schéma').'</p>']),
        im_block('galerie', ['images' => [$img['fluvial'], $img['saulny'], $img['aerien'], $img['avenir']]]),
    ],
]);

im_fields($videosPage, [
    'chapo' => 'Plongez dans l’univers de l’Euro-Métropole de Metz à travers des vidéos qui mettent en lumière ses atouts et son dynamisme.',
    'liste_titre' => 'Découvrir nos vidéos',
]);

// S'installer
im_fields($installer, [
    'chapo' => 'Étudiants, actifs, familles ou entrepreneurs : l’agence Inspire Metz vous accompagne pour réussir votre installation dans l’Euro-Métropole de Metz.',
    'blocs' => [im_block('cartes', ['cartes' => [
        ['titre' => '10 raisons de s’installer', 'lien' => im_link(get_permalink($raisons), '10 raisons de s’installer'), 'image' => $img['aerien']],
        ['titre' => 'Guide d’accueil des nouveaux arrivants', 'lien' => im_link(get_permalink($guide), 'Guide d’accueil'), 'image' => $img['etudiants']],
        ['titre' => 'Devenir ambassadeur', 'lien' => im_link(get_permalink($ambassadeur), 'Ambassadeur'), 'image' => $img['amb3']],
    ]], 'sombre')],
]);

$reasons = implode('', array_map(fn ($row) => '<li>'.esc_html($row[1]).'</li>', array_filter($live['10-raisons-de-sinstaller-dans-leurometropole-de-metz'], fn ($row) => $row[0] === 'li')));

im_fields($raisons, [
    'chapo' => im_live('10-raisons-de-sinstaller-dans-leurometropole-de-metz', 'Découvrez pourquoi'),
    'bandeau' => $img['aerien'],
    'blocs' => [
        im_block('texte', ['texte' => '<h2>Changez votre point de vue…</h2><p>…expérimentez une destination accessible, innovante et durable !</p><ol>'.$reasons.'</ol>']),
        im_block('encart', ['titre' => 'Prêt à franchir le pas ?', 'texte' => 'Logement, emploi, écoles, mobilité : l’équipe d’Inspire Metz vous accompagne dans chaque étape de votre installation.', 'lien' => im_link(get_permalink($guide), 'Guide d’accueil des nouveaux arrivants')]),
    ],
]);

im_fields($guide, [
    'chapo' => im_live('guide-daccueil-des-nouveaux-arrivants', 'Nouveaux habitants'),
    'bandeau' => $img['etudiants'],
    'blocs' => [
        im_block('texte', ['texte' => '<h2>Un guichet unique d’accueil</h2><p>'.im_live('guide-daccueil-des-nouveaux-arrivants', 'Grâce à un guichet').'</p><p>'.im_live('guide-daccueil-des-nouveaux-arrivants', 'Vous êtes étudiants').'</p>']),
        im_block('liens', ['titre' => 'Les guides à consulter', 'texte' => 'Toutes les informations utiles à votre installation.', 'liens' => [
            ['type' => 'lien', 'lien' => im_link('https://inspire-metz.com/sinstaller-sur-le-territoire/guide-daccueil-des-nouveaux-arrivants/', 'Guide d’accueil des nouveaux arrivants', true)],
            ['type' => 'lien', 'lien' => im_link(get_permalink($raisons), '10 raisons de s’installer dans l’Euro-Métropole de Metz')],
        ]]),
        im_block('contact', ['titre' => 'Une question sur votre installation ?', 'texte' => 'L’équipe de l’agence est à votre écoute pour faciliter votre intégration.', 'intitule' => 'Agence Inspire Metz', 'adresse' => '2 Place d’Armes<br>J. F. Blondel CS 80367<br>57007 Cedex 1 Metz', 'telephone' => '+33 (0)3 87 16 21 45']),
    ],
]);

im_fields($ambassadeur, [
    'chapo' => im_live('ambassadeur', 'Pour partager des valeurs'),
    'blocs' => [
        im_block('colonnes', ['colonnes' => [
            ['picto' => $img['icon_gauche'], 'titre' => 'Qu’est-ce qu’un ambassadeur ?', 'texte' => im_live('ambassadeur', 'Messin de naissance')],
            ['picto' => $img['icon_droite'], 'titre' => 'Quel est votre rôle ?', 'texte' => im_live('ambassadeur', 'Promouvoir notre destination')],
        ], 'lien' => im_link('#formulaire', 'Devenir ambassadeur')], 'sombre'),
        im_block('mosaique', ['images' => [$img['amb3'], $img['amb4'], $img['amb1'], $img['amb2']]], 'sombre'),
        im_block('formulaire', ['formulaire' => 'ambassadeur', 'titre' => 'Devenez Ambassadeur de l’Euro-Métropole de Metz', 'texte' => 'Pour partager des valeurs, participer à une dynamique de territoire. Le Club des Ambassadeurs de l’Euro-Métropole de Metz est ce qui nous fera réussir ensemble.']),
    ],
]);

// L'agence
im_fields($agence, [
    'chapo' => 'L’agence d’attractivité de l’Euro-Métropole de Metz, au service des 46 communes : tourisme, tourisme d’affaires et attractivité économique.',
    'blocs' => [im_block('cartes', ['cartes' => [
        ['titre' => 'Présentation et missions', 'lien' => im_link(get_permalink($presentation), 'Présentation et missions'), 'image' => $img['missions']],
        ['titre' => 'Actualités', 'lien' => im_link(get_permalink($news), 'Actualités'), 'image' => $img['roue']],
        ['titre' => 'Espace presse', 'lien' => im_link(get_permalink($presse), 'Espace presse'), 'image' => $img['office']],
    ]], 'sombre')],
]);

im_fields($presentation, [
    'chapo' => im_live('presentation-et-missions', 'L’agence d’attractivité qui accompagne'),
    'bandeau' => $img['missions'],
    'blocs' => [
        im_block('texte', ['texte' => '<p>'.im_live('presentation-et-missions', 'Inspire Metz est l’agence d’attractivité de').'</p>']),
        im_block('texte_image', ['titre' => 'Un pôle Attractivité économique pour accompagner les acteurs', 'texte' => '<p>'.im_live('presentation-et-missions', 'Inspire Metz est une agence engagée').'</p><p>'.im_live('presentation-et-missions', 'Vous souhaitez vous développer').'</p><p><strong>Pôle Attractivité économique</strong> : +33 (0)3 87 16 21 45</p>', 'image' => $img['photonique'], 'position' => 'droite', 'lien' => im_link('https://www.investinmetz.com/', 'Invest in Metz', true)]),
        im_block('texte_image', ['titre' => 'Un Bureau des Congrès pour une destination d’affaires durable et innovante', 'texte' => '<p>'.im_live('presentation-et-missions', 'Sa mission principale').'</p><p>'.im_live('presentation-et-missions', 'Vous souhaitez organiser').'</p><p><strong>Bureau des Congrès</strong> : +33 (0)3 87 16 96 85</p>', 'image' => $img['mcrs'], 'position' => 'gauche', 'lien' => im_link('https://congres-metz.com/', 'Congrès Metz', true)]),
        im_block('texte_image', ['titre' => 'Un pôle Tourisme pour faire découvrir un territoire créatif et durable', 'texte' => '<p>'.im_live('presentation-et-missions', 'Le pôle Tourisme, du fait').'</p><p><strong>Pôle Tourisme</strong> : +33 (0)3 87 39 00 00</p>', 'image' => $img['office'], 'position' => 'droite', 'lien' => im_link('https://tourisme-metz.com/', 'Tourisme Metz', true)]),
        im_block('chiffres', ['chiffres' => [
            ['valeur' => '46', 'libelle' => 'communes au service desquelles agit l’agence'],
            ['valeur' => '3', 'libelle' => 'missions : tourisme, tourisme d’affaires, attractivité économique'],
            ['valeur' => '5', 'libelle' => 'pôles d’expertise'],
        ]]),
        im_block('texte', ['texte' => '<h2>Une agence, cinq pôles</h2><p>'.im_live('presentation-et-missions', 'Le Pôle communication').'</p>']),
        im_block('liens', ['titre' => 'Documents', 'texte' => '', 'liens' => [
            ['type' => 'fichier', 'fichier' => $img['ra_pdf'], 'libelle' => 'Rapport d’activité 2025', 'vignette' => $img['ra_vignette']],
            ['type' => 'lien', 'lien' => im_link(get_permalink($presse), 'Tous les communiqués de presse')],
        ]]),
    ],
]);

// Pages légales
$legal = fn (string $html) => ['blocs' => [im_block('texte', ['texte' => $html])]];

im_fields($mentions, $legal(
    '<h2>Éditeur du site</h2><p>Inspire Metz, agence d’attractivité de l’Euro-Métropole de Metz<br>2 Place d’Armes – J. F. Blondel, CS 80367, 57007 Metz Cedex 1<br>Tél. : +33 (0)3 87 39 00 00</p>'
    .'<h2>Hébergement</h2><p>OVH – 2 rue Kellermann, 59100 Roubaix.</p>'
    .'<h2>Propriété intellectuelle</h2><p>'.im_live('mentions-legales', 'https://www.inspire-metz.com/ est propriétaire').'</p>'
    .'<h2>Données personnelles</h2><p>'.im_live('mentions-legales', 'Le Client est informé').'</p><p>Pour en savoir plus, consultez notre page <a href="'.get_permalink($privacy).'">confidentialité</a>.</p>'
    .'<h2>Crédits photos</h2><p>'.im_live('mentions-legales', 'Photos de la page d’accueil').'</p>'
));

im_fields($privacy, $legal(
    '<h2>Données collectées</h2><p>Les formulaires du site (contact, inscription à la newsletter, candidature au Club des Ambassadeurs) collectent uniquement les informations nécessaires au traitement de votre demande : nom, prénom, coordonnées et, le cas échéant, votre message.</p>'
    .'<h2>Utilisation et conservation</h2><p>Ces données sont utilisées par l’agence Inspire Metz pour vous répondre, vous adresser sa newsletter ou animer le Club des Ambassadeurs. Elles ne sont jamais cédées à des tiers et sont conservées au maximum 36 mois après notre dernier échange.</p>'
    .'<h2>Vos droits</h2><ul><li>droit d’accès et de rectification</li><li>droit à l’effacement</li><li>droit d’opposition et de limitation du traitement</li><li>droit de retirer votre consentement à tout moment</li></ul>'
    .'<p>Pour exercer ces droits, écrivez-nous via la page <a href="'.get_permalink($contact).'">contact</a> ou à l’adresse : 2 Place d’Armes – J. F. Blondel, CS 80367, 57007 Metz Cedex 1. Vous pouvez également saisir la <a href="https://www.cnil.fr/fr/plaintes">CNIL</a>.</p>'
));

im_fields($cookies, $legal(
    '<h2>Utilisation des cookies</h2><p>Ce site n’utilise que des cookies techniques, indispensables à son fonctionnement (sécurité des formulaires, préférences d’affichage). Aucun cookie de mesure d’audience ou publicitaire n’est déposé sans votre consentement.</p>'
    .'<h2>Contenus tiers</h2><p>Les vidéos YouTube ne sont chargées qu’au moment où vous lancez leur lecture, via le domaine youtube-nocookie.com.</p>'
));

/*
|--------------------------------------------------------------------------
| Réglages Inspire Metz
|--------------------------------------------------------------------------
*/

im_log('Réglages…');

im_fields('option', [
    'adresse' => "2 Place d’Armes\nJ. F. Blondel CS 80367\n57007 Cedex 1 Metz",
    'telephone' => '+33 (0)3 87 39 00 00',
    'email' => 'tourisme@inspire-metz.com',
    'contacts' => [
        ['service' => 'Développement économique', 'telephone' => '+33 (0)3 87 16 21 45'],
        ['service' => 'Tourisme d’affaires', 'telephone' => '+33 (0)3 87 16 96 85'],
    ],
    'hashtag' => '#INSPIREMETZ',
    'facebook' => 'https://www.facebook.com/InspireMetz',
    'linkedin' => 'https://fr.linkedin.com/company/inspire-metz',
    'x' => 'https://x.com/inspire_metz',
    'instagram' => 'https://www.instagram.com/tourismemetz/',
    'missions' => [
        ['couleur' => 'visiter', 'libelle' => 'Visiter', 'titre' => 'Destination eco-touristique', 'image' => $img['fluvial'], 'texte' => im_live('inspire-metz.com', 'Le Pôle Tourisme')."\n\n".im_live('inspire-metz.com', 'Metz, idéalement située'), 'lien' => im_link('https://tourisme-metz.com/', 'Accéder au site', true)],
        ['couleur' => 'organiser', 'libelle' => 'Organiser', 'titre' => 'Événements professionnels', 'image' => $img['mcrs'], 'texte' => im_live('inspire-metz.com', 'Le territoire de l’Euro-Métropole de Metz dispose')."\n\n".im_live('inspire-metz.com', 'Notre destination favorise')."\n\n".rtrim(im_live('inspire-metz.com', 'L’équipe du Convention Bureau'), '0123456789'), 'lien' => im_link('https://congres-metz.com/', 'Accéder au site', true)],
        ['couleur' => 'investir', 'libelle' => 'Investir', 'titre' => 'Un territoire d’opportunités économiques et d’innovation', 'image' => $img['photonique'], 'texte' => im_live('inspire-metz.com', 'À 85 minutes de Paris au sein')."\n\n".im_live('inspire-metz.com', 'L’équipe de développeurs'), 'lien' => im_link('https://www.investinmetz.com/', 'Accéder au site', true)],
    ],
    'missions_baseline' => 'Une équipe aux côtés des acteurs pour faire rayonner l’Euro-Métropole de Metz.',
    'newsletter_titre' => 'Pour suivre l’actualité de l’Euro-Métropole de Metz,',
    'newsletter_titre_accent' => 'inscrivez-vous à notre newsletter !',
    'newsletter_texte' => 'Recevez tous les 15 jours dans votre boîte mail les informations économiques, touristiques et de tourisme d’affaires du territoire et de nos partenaires.',
    'mention_agence' => 'L’agence d’attractivité Inspire Metz est au service des 46 communes de l’Euro-Métropole de Metz.',
    'logo_tutelle' => $img['emm_couleur'],
    'logo_region' => $img['grand_est_couleur'],
    'mention_region' => 'L’agence Inspire Metz est soutenue financièrement par la Région Grand Est sur le volet économique.',
    'page_contact' => $contact,
    'page_newsletter' => $newsletters,
    'page_presse' => $presse,
    'page_videos' => $videosPage,
    'page_agence' => $presentation,
    'page_territoire' => $communesPage,
]);

/*
|--------------------------------------------------------------------------
| Menus
|--------------------------------------------------------------------------
*/

im_log('Menus…');

function im_menu(string $name, string $location, array $items): void
{
    $menu = wp_get_nav_menu_object($name);
    $id = $menu ? $menu->term_id : wp_create_nav_menu($name);

    // Tous statuts confondus : un élément « programmé » échappe à wp_get_nav_menu_items().
    foreach (get_objects_in_term($id, 'nav_menu') as $item) {
        wp_delete_post((int) $item, true);
    }

    $add = function (array $items, int $parent = 0) use (&$add, $id) {
        foreach ($items as $position => $item) {
            [$title, $target, $children] = $item + [2 => []];

            $args = ['menu-item-title' => $title, 'menu-item-status' => 'publish', 'menu-item-parent-id' => $parent, 'menu-item-position' => $position + 1];
            $args += is_int($target)
                ? ['menu-item-object-id' => $target, 'menu-item-object' => 'page', 'menu-item-type' => 'post_type']
                : ['menu-item-url' => $target, 'menu-item-type' => 'custom'];

            $itemId = wp_update_nav_menu_item($id, 0, $args);
            $add($children, $itemId);
        }
    };

    $add($items);

    $locations = get_theme_mod('nav_menu_locations', []);
    $locations[$location] = $id;
    set_theme_mod('nav_menu_locations', $locations);
}

im_menu('Menu principal', 'primary_navigation', [
    ['Découvrir l’Euro-Métropole de Metz', $decouvrir, [
        ['Les 46 communes de l’Euro-Métropole', $communesPage],
        ['Destination durable', $durable],
        ['Vidéos', $videosPage],
    ]],
    ['S’installer sur le territoire', $installer, [
        ['10 raisons de s’installer', $raisons],
        ['Guide d’accueil des nouveaux arrivants', $guide],
        ['Ambassadeur', $ambassadeur],
    ]],
    ['L’agence Inspire Metz', $agence, [
        ['Présentation et missions', $presentation],
        ['Actualités', $news],
        ['Espace presse', $presse],
        ['Newsletters', $newsletters],
    ]],
]);

im_menu('Menu secondaire', 'secondary_navigation', [
    ['Actualités', $news],
    ['Newsletters', $newsletters],
    ['Espace presse', $presse],
    ['Devenir ambassadeur', $ambassadeur],
    ['Nous contacter', $contact],
]);

im_menu('Pied de page', 'footer_navigation', [
    ['Nous contacter', $contact],
    ['Mentions légales', $mentions],
    ['Gestion des cookies', $cookies],
]);

/*
| Filet de sécurité « changement d'heure » : tout contenu de démo passé en
| « programmé » à cause d'une date GMT calculée dans le futur est publié.
*/
global $wpdb;

$now = current_time('mysql', true);
$fixed = $wpdb->query($wpdb->prepare(
    "UPDATE {$wpdb->posts} SET post_status = 'publish', post_date_gmt = LEAST(post_date_gmt, %s)
     WHERE post_status = 'future' AND post_date_gmt <= DATE_ADD(%s, INTERVAL 2 HOUR)",
    $now,
    $now
));

if ($fixed) {
    im_log("{$fixed} contenu(s) republié(s) (heure ambiguë du changement d'heure).");
    wp_cache_flush();
}

// Les compteurs de menus ne comptent que les éléments publiés au moment de l'ajout.
wp_update_term_count_now(array_map(fn ($menu) => $menu->term_taxonomy_id, wp_get_nav_menus()), 'nav_menu');

flush_rewrite_rules();

WP_CLI::success('Contenu de démonstration importé.');

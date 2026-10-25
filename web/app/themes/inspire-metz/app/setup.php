<?php

/**
 * Theme setup.
 */

namespace App;

use Illuminate\Support\Facades\Vite;

/**
 * Inject styles into the block editor.
 *
 * @return array
 */
add_filter('block_editor_settings_all', function ($settings) {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('{$style}')",
    ];

    return $settings;
});

/**
 * Inject scripts into the block editor.
 *
 * @return void
 */
add_action('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    if (! Vite::isRunningHot()) {
        $dependencies = json_decode(Vite::content('editor.deps.json'));

        foreach ($dependencies as $dependency) {
            if (! wp_script_is($dependency)) {
                wp_enqueue_script($dependency);
            }
        }
    }
    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * Use the generated theme.json file.
 *
 * @return string
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * Disable on-demand block asset loading.
 *
 * @link https://core.trac.wordpress.org/ticket/61965
 */
add_filter('should_load_separate_core_block_assets', '__return_false');

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Menu principal (en-tête)', 'inspire-metz'),
        'secondary_navigation' => __('Menu secondaire (panneau du menu)', 'inspire-metz'),
        'footer_navigation' => __('Liens du pied de page', 'inspire-metz'),
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

/**
 * Pages et actualités se construisent avec les blocs SCF (app/Fields),
 * l'éditeur de blocs n'est donc pas utilisé.
 *
 * @return bool
 */
add_filter('use_block_editor_for_post_type', '__return_false');

/**
 * Sans éditeur de blocs, leurs feuilles de style sont inutiles en front ;
 * hors @layer, elles écraseraient en plus les utilitaires Tailwind.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    foreach (['wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles'] as $handle) {
        wp_dequeue_style($handle);
    }
}, 100);

/**
 * Destinataires des notifications de formulaires, saisis dans
 * Inspire Metz → Formulaires (mu-plugin inspire-metz-core).
 *
 * @return array
 */
add_filter('inspire_metz/form_recipients', function ($recipients, $form) {
    if (! function_exists('get_field')) {
        return $recipients;
    }

    $configured = trim((string) get_field("notification_{$form}", 'option'));

    return $configured ? array_map('trim', explode(',', $configured)) : $recipients;
}, 10, 2);

/**
 * Favicons de l'agence (mêmes fichiers que le site en ligne). Une icône définie
 * dans Réglages → Général (« Icône du site ») reste prioritaire.
 *
 * @return void
 */
$favicons = function () {
    if (has_site_icon()) {
        return;
    }

    $icon = fn ($size) => Vite::asset("resources/images/favicon/favicon-{$size}.png");

    printf('<link rel="icon" href="%s" sizes="32x32">'."\n", esc_url($icon('32x32')));
    printf('<link rel="icon" href="%s" sizes="192x192">'."\n", esc_url($icon('192x192')));
    printf('<link rel="apple-touch-icon" href="%s">'."\n", esc_url($icon('180x180')));
    printf('<meta name="msapplication-TileImage" content="%s">'."\n", esc_url($icon('270x270')));
    echo '<meta name="theme-color" content="#001231">'."\n";
};

add_action('wp_head', $favicons, 2);
add_action('login_head', $favicons);
add_action('admin_head', $favicons);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
    ] + $config);
});

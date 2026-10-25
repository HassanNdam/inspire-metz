<?php

namespace InspireMetz\Core;

/**
 * Types de contenu éditoriaux.
 *
 * Les actualités utilisent les articles natifs de WordPress.
 * Les champs de chaque type sont déclarés dans le thème (app/Fields).
 */
class PostTypes
{
    public static function boot(): void
    {
        add_action('init', [self::class, 'register']);
        add_action('init', [self::class, 'renamePosts']);
    }

    public static function register(): void
    {
        self::type('communique', __('Communiqués de presse', 'inspire-metz'), __('Communiqué de presse', 'inspire-metz'), [
            'menu_icon' => 'dashicons-media-document',
            'menu_position' => 6,
            'supports' => ['title', 'thumbnail'],
        ]);

        self::type('jeem', __('Magazine Jeem', 'inspire-metz'), __('Numéro de Jeem', 'inspire-metz'), [
            'menu_icon' => 'dashicons-book-alt',
            'menu_position' => 7,
            'supports' => ['title', 'thumbnail'],
        ]);

        self::type('lettre_info', __('Newsletters envoyées', 'inspire-metz'), __('Newsletter envoyée', 'inspire-metz'), [
            'menu_icon' => 'dashicons-megaphone',
            'menu_position' => 8,
            'supports' => ['title', 'excerpt'],
        ]);

        self::type('video', __('Vidéos', 'inspire-metz'), __('Vidéo', 'inspire-metz'), [
            'menu_icon' => 'dashicons-video-alt3',
            'menu_position' => 9,
            'supports' => ['title', 'excerpt', 'thumbnail', 'page-attributes'],
        ]);

        self::type('commune', __('Communes', 'inspire-metz'), __('Commune', 'inspire-metz'), [
            'menu_icon' => 'dashicons-location-alt',
            'menu_position' => 10,
            'supports' => ['title', 'page-attributes'],
        ]);
    }

    /**
     * « Articles » devient « Actualités » dans l'administration.
     */
    public static function renamePosts(): void
    {
        $object = get_post_type_object('post');

        if (! $object) {
            return;
        }

        $labels = &$object->labels;
        $labels->name = __('Actualités', 'inspire-metz');
        $labels->singular_name = __('Actualité', 'inspire-metz');
        $labels->menu_name = __('Actualités', 'inspire-metz');
        $labels->name_admin_bar = __('Actualité', 'inspire-metz');
        $labels->all_items = __('Toutes les actualités', 'inspire-metz');
        $labels->add_new_item = __('Ajouter une actualité', 'inspire-metz');
        $labels->edit_item = __('Modifier l’actualité', 'inspire-metz');
        $object->menu_icon = 'dashicons-format-aside';
    }

    /**
     * Ces contenus s'affichent dans des listes (Espace presse, Vidéos…),
     * sans page individuelle : ils ne sont donc pas « publicly_queryable ».
     */
    protected static function type(string $key, string $plural, string $singular, array $args = []): void
    {
        register_post_type($key, $args + [
            'labels' => [
                'name' => $plural,
                'singular_name' => $singular,
                'menu_name' => $plural,
                'all_items' => $plural,
                'add_new' => __('Ajouter', 'inspire-metz'),
                'add_new_item' => sprintf(__('Ajouter : %s', 'inspire-metz'), $singular),
                'edit_item' => sprintf(__('Modifier : %s', 'inspire-metz'), $singular),
                'search_items' => __('Rechercher', 'inspire-metz'),
                'not_found' => __('Aucun élément.', 'inspire-metz'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'rewrite' => false,
        ]);
    }
}

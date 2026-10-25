<?php

namespace App\Support;

/**
 * Accès en cache aux réglages globaux (Inspire Metz → options SCF) et aux menus.
 */
class Site
{
    protected static array $cache = [];

    public static function option(string $key, mixed $default = null): mixed
    {
        $value = function_exists('get_field') ? get_field($key, 'option') : null;

        return ($value === null || $value === '' || $value === false || $value === []) ? $default : $value;
    }

    /**
     * Coordonnées et réseaux, normalisés pour les vues.
     */
    public static function contact(): array
    {
        return self::$cache['contact'] ??= [
            'address' => self::option('adresse', '2 Place d’Armes<br>J. F. Blondel CS 80367<br>57007 Cedex 1 Metz'),
            'phone' => self::option('telephone', '+33 (0)3 87 39 00 00'),
            'email' => self::option('email'),
            'services' => self::option('contacts', []),
        ];
    }

    /**
     * @return array<int, array{network: string, label: string, url: string}>
     */
    public static function socials(): array
    {
        return self::$cache['socials'] ??= collect([
            'facebook' => 'Facebook',
            'linkedin' => 'LinkedIn',
            'x' => 'X',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
        ])->map(fn ($label, $network) => [
            'network' => $network,
            'label' => $label,
            'url' => self::option($network),
        ])->filter(fn ($social) => $social['url'])->values()->all();
    }

    /**
     * Missions (Visiter, Organiser, Investir) avec un identifiant stable pour les modales.
     */
    public static function missions(): array
    {
        return self::$cache['missions'] ??= collect(self::option('missions', []))
            ->values()
            ->map(fn ($mission, $index) => $mission + ['id' => 'mission-'.($index + 1)])
            ->all();
    }

    public static function pageUrl(string $key, string $fallback = '#'): string
    {
        $id = self::option("page_{$key}");

        return $id ? (string) get_permalink($id) : $fallback;
    }

    /**
     * Menu WordPress sous forme d'arbre (2 niveaux), avec l'état « courant ».
     *
     * @return array<int, array{title: string, url: string, target: ?string, active: bool, children: array}>
     */
    public static function menu(string $location): array
    {
        if (isset(self::$cache["menu.{$location}"])) {
            return self::$cache["menu.{$location}"];
        }

        $locations = get_nav_menu_locations();
        $items = isset($locations[$location]) ? (wp_get_nav_menu_items($locations[$location]) ?: []) : [];

        _wp_menu_item_classes_by_context($items);

        $tree = [];
        $children = [];

        foreach ($items as $item) {
            $node = [
                'title' => $item->title,
                'url' => $item->url,
                'target' => $item->target ?: null,
                'active' => (bool) array_intersect(['current-menu-item', 'current-menu-ancestor', 'current-menu-parent', 'current_page_parent'], (array) $item->classes),
                'children' => [],
            ];

            if ($item->menu_item_parent) {
                $children[$item->menu_item_parent][] = $node;
            } else {
                $tree[$item->ID] = $node;
            }
        }

        foreach ($children as $parent => $nodes) {
            if (isset($tree[$parent])) {
                $tree[$parent]['children'] = $nodes;
            }
        }

        return self::$cache["menu.{$location}"] = array_values($tree);
    }

    /**
     * Description courte d'un fichier : « PDF - 1,2 Mo ».
     */
    public static function fileMeta(int $attachment): string
    {
        $path = get_attached_file($attachment);
        $type = strtoupper((string) pathinfo((string) $path, PATHINFO_EXTENSION));
        $size = $path && file_exists($path) ? size_format(filesize($path), 1) : null;

        return implode(' - ', array_filter([$type, $size ? str_replace('.', ',', $size) : null]));
    }

    /**
     * Lien « tel: » à partir d'un numéro affiché (« +33 (0)3 87 39 00 00 »).
     */
    public static function tel(string $phone): string
    {
        return 'tel:'.preg_replace('/[^0-9+]/', '', str_replace('(0)', '', $phone));
    }

    /**
     * Contenu brut d'un SVG de resources/images (logo inline, recolorable).
     */
    public static function svg(string $path): string
    {
        return self::$cache["svg.{$path}"] ??= (string) @file_get_contents(resource_path("images/{$path}"));
    }
}

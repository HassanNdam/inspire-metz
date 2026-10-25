<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;
use WP_Query;

/**
 * Pages de listes : Espace presse, Newsletters, Vidéos.
 * La pagination passe par ?pagination=N (bouton « Charger plus »).
 */
class Listing extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'template-presse',
        'template-newsletters',
        'template-videos',
    ];

    protected array $queries = [];

    /**
     * Data to be passed to view before rendering.
     */
    public function with(): array
    {
        return [
            'fields' => $this->fields(),
            'jeems' => $this->view->name() === 'template-presse' ? $this->jeems() : [],
            'communiques' => $this->communiques(),
            'lettres' => $this->lettres(),
            'videos' => $this->videos(),
            'nextUrl' => $this->nextUrl(),
        ];
    }

    protected function fields(): array
    {
        return (function_exists('get_fields') ? get_fields() : null) ?: [];
    }

    protected function jeems(): array
    {
        return get_posts(['post_type' => 'jeem', 'numberposts' => 3]);
    }

    protected function communiques(): WP_Query
    {
        return $this->paginate('communique', 12);
    }

    protected function lettres(): WP_Query
    {
        return $this->paginate('lettre_info', 12);
    }

    protected function videos(): WP_Query
    {
        return $this->paginate('video', 12, ['orderby' => ['menu_order' => 'ASC', 'date' => 'DESC']]);
    }

    /**
     * URL de la page suivante d'une liste, ou null.
     */
    protected function nextUrl(): \Closure
    {
        return fn (WP_Query $query) => $this->page() < $query->max_num_pages
            ? add_query_arg('pagination', $this->page() + 1, get_permalink())
            : null;
    }

    protected function paginate(string $type, int $perPage, array $args = []): WP_Query
    {
        return $this->queries[$type] ??= new WP_Query($args + [
            'post_type' => $type,
            'posts_per_page' => $perPage,
            'paged' => $this->page(),
            'no_found_rows' => false,
        ]);
    }

    protected function page(): int
    {
        return max(1, (int) ($_GET['pagination'] ?? 1));
    }
}

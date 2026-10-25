<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Post extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.page-header',
        'partials.content',
        'partials.content-*',
    ];

    /**
     * Data to be passed to view before rendering.
     */
    public function with(): array
    {
        if ($this->view->name() !== 'partials.page-header') {
            return ['title' => $this->title(), 'pagination' => $this->pagination()];
        }

        return [
            'title' => $this->title(),
            'chapo' => $this->chapo(),
            'bandeau' => $this->bandeau(),
            'breadcrumbs' => $this->breadcrumbs(),
            'date' => $this->date(),
        ];
    }

    /**
     * Retrieve the post title.
     */
    protected function title(): string
    {
        if ($this->view->name() !== 'partials.page-header') {
            return get_the_title();
        }

        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return get_the_title($home);
            }

            return __('Actualités', 'inspire-metz');
        }

        if (is_archive()) {
            return get_the_archive_title();
        }

        if (is_search()) {
            return sprintf(
                /* translators: %s is replaced with the search query */
                __('Résultats pour « %s »', 'inspire-metz'),
                get_search_query()
            );
        }

        if (is_404()) {
            return __('Page introuvable', 'inspire-metz');
        }

        return get_the_title();
    }

    /**
     * Chapô saisi dans « En-tête de page ».
     */
    protected function chapo(): ?string
    {
        $id = $this->contextId();

        return $id && function_exists('get_field') ? (get_field('chapo', $id) ?: null) : null;
    }

    /**
     * Image bandeau facultative (pages de contenu).
     */
    protected function bandeau(): ?int
    {
        $id = $this->contextId();

        if (! $id || ! function_exists('get_field') || is_singular('post')) {
            return null;
        }

        return get_field('bandeau', $id) ?: null;
    }

    /**
     * Fil d'Ariane du site en ligne : Accueil | parents | page courante.
     *
     * @return array<int, array{title: string, url: ?string}>
     */
    protected function breadcrumbs(): array
    {
        $crumbs = [['title' => __('Accueil', 'inspire-metz'), 'url' => home_url('/')]];

        if (is_singular('post')) {
            $news = get_option('page_for_posts');
            $crumbs[] = ['title' => $news ? get_the_title($news) : __('Actualités', 'inspire-metz'), 'url' => $news ? get_permalink($news) : null];
            $crumbs[] = ['title' => get_the_title(), 'url' => null];

            return $crumbs;
        }

        $id = $this->contextId();

        foreach (array_reverse($id ? get_post_ancestors($id) : []) as $ancestor) {
            $crumbs[] = ['title' => get_the_title($ancestor), 'url' => get_permalink($ancestor)];
        }

        $crumbs[] = ['title' => $this->title(), 'url' => null];

        return $crumbs;
    }

    /**
     * Date affichée sous le titre des actualités.
     */
    protected function date(): ?string
    {
        return is_singular('post') ? get_the_date('d F Y') : null;
    }

    /**
     * Retrieve the pagination links.
     */
    protected function pagination(): string
    {
        return wp_link_pages([
            'echo' => 0,
            'before' => '<p>'.__('Pages :', 'inspire-metz'),
            'after' => '</p>',
        ]);
    }

    /**
     * Page dont on lit les champs d'en-tête (la page des articles sur l'accueil du blog).
     */
    protected function contextId(): ?int
    {
        if (is_home()) {
            return (int) get_option('page_for_posts') ?: null;
        }

        return is_singular() ? get_the_ID() : null;
    }
}

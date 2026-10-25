<?php

namespace App\View\Composers;

use App\Support\Site;
use Roots\Acorn\View\Composer;

/**
 * Page d'accueil (maquette p. 6) : champs du groupe « Page d'accueil ».
 */
class FrontPage extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'front-page',
    ];

    protected static ?array $fields = null;

    /**
     * Data to be passed to view before rendering.
     */
    public function with(): array
    {
        return [
            'home' => $this->home(),
            'shards' => $this->shards(),
            'missions' => $this->missions(),
            'communes' => $this->communes(),
            'news' => $this->news(),
            'newsUrl' => $this->newsUrl(),
            'pressCovers' => $this->pressCovers(),
            'socials' => $this->socials(),
            'hashtag' => $this->hashtag(),
        ];
    }

    protected function home(): array
    {
        return self::$fields ??= (function_exists('get_fields') ? get_fields(get_option('page_on_front')) : null) ?: [];
    }

    /**
     * Les trois éclats du hero (gauche, bas, droite), chacun relié à une mission
     * dans l'ordre du site en ligne : Organiser, Investir, Visiter.
     * À défaut de photo dédiée, l'éclat reprend l'image de la mission.
     */
    protected function shards(): array
    {
        $missions = collect(Site::missions())->keyBy('couleur');
        $images = array_values($this->home()['hero_images'] ?? []);

        return collect(['left' => 'organiser', 'bottom' => 'investir', 'right' => 'visiter'])
            ->values()
            ->map(function ($key, $index) use ($missions, $images) {
                $mission = $missions[$key] ?? null;
                $image = $images[$index] ?? $mission['image'] ?? null;

                return $image ? ['image' => $image, 'mission' => $mission, 'position' => ['left', 'bottom', 'right'][$index]] : null;
            })
            ->filter()
            ->all();
    }

    protected function missions(): array
    {
        return Site::missions();
    }

    protected function communes(): array
    {
        return get_posts([
            'post_type' => 'commune',
            'numberposts' => -1,
            'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
            'fields' => 'ids',
        ]);
    }

    protected function news(): array
    {
        return get_posts([
            'post_type' => 'post',
            'numberposts' => (int) ($this->home()['actualites_nombre'] ?? 6) ?: 6,
            'ignore_sticky_posts' => true,
        ]);
    }

    protected function newsUrl(): string
    {
        $page = get_option('page_for_posts');

        return $page ? get_permalink($page) : home_url('/');
    }

    /**
     * Couvertures pour le collage de l'encart presse : derniers Jeem et communiqués.
     */
    protected function pressCovers(): array
    {
        return collect(['jeem', 'communique'])
            ->flatMap(fn ($type) => get_posts(['post_type' => $type, 'numberposts' => 5, 'fields' => 'ids']))
            ->map(fn ($id) => get_post_thumbnail_id($id))
            ->filter()
            ->take(9)
            ->values()
            ->all();
    }

    protected function socials(): array
    {
        return Site::socials();
    }

    protected function hashtag(): string
    {
        return Site::option('hashtag', '#INSPIREMETZ');
    }
}

<?php

namespace App\View\Composers;

use App\Support\Site as SiteData;
use Roots\Acorn\View\Composer;

/**
 * Données communes à l'en-tête, au menu et au pied de page.
 */
class Site extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'layouts.app',
    ];

    /**
     * Data to be passed to view before rendering.
     */
    public function with(): array
    {
        return [
            'contact' => $this->contact(),
            'socials' => $this->socials(),
            'missions' => $this->missions(),
            'primaryMenu' => $this->primaryMenu(),
            'secondaryMenu' => $this->secondaryMenu(),
            'footerMenu' => $this->footerMenu(),
            'newsletterUrl' => $this->newsletterUrl(),
            'headerTheme' => $this->headerTheme(),
            'languages' => $this->languages(),
            'footer' => $this->footer(),
        ];
    }

    protected function contact(): array
    {
        return SiteData::contact();
    }

    protected function socials(): array
    {
        return SiteData::socials();
    }

    protected function missions(): array
    {
        return SiteData::missions();
    }

    protected function primaryMenu(): array
    {
        return SiteData::menu('primary_navigation');
    }

    protected function secondaryMenu(): array
    {
        return SiteData::menu('secondary_navigation');
    }

    protected function footerMenu(): array
    {
        return SiteData::menu('footer_navigation');
    }

    protected function newsletterUrl(): string
    {
        return SiteData::pageUrl('newsletter');
    }

    /**
     * En-tête clair (texte foncé) sur les pages à fond blanc : contact, 404.
     */
    protected function headerTheme(): string
    {
        return is_404() || is_page_template('template-contact.blade.php') ? 'light' : 'dark';
    }

    /**
     * Langues disponibles : Polylang si présent, sinon français seul.
     *
     * @return array<int, array{code: string, url: string, current: bool}>
     */
    protected function languages(): array
    {
        if (function_exists('pll_the_languages')) {
            return collect(pll_the_languages(['raw' => 1, 'hide_if_empty' => 0]))
                ->map(fn ($language) => [
                    'code' => strtoupper($language['slug']),
                    'url' => $language['url'],
                    'current' => (bool) $language['current_lang'],
                ])->values()->all();
        }

        return [['code' => 'FR', 'url' => home_url('/'), 'current' => true]];
    }

    protected function footer(): array
    {
        return [
            'title' => SiteData::option('newsletter_titre', __('Pour suivre l’actualité de l’Euro-Métropole de Metz,', 'inspire-metz')),
            'accent' => SiteData::option('newsletter_titre_accent', __('inscrivez-vous à notre newsletter !', 'inspire-metz')),
            'text' => SiteData::option('newsletter_texte', __('Recevez tous les 15 jours dans votre boîte mail les informations économiques, touristiques et de tourisme d’affaires du territoire et de nos partenaires.', 'inspire-metz')),
            'mention' => SiteData::option('mention_agence', __('L’agence d’attractivité Inspire Metz est au service des 46 communes de l’Euro-Métropole de Metz.', 'inspire-metz')),
            'logo' => SiteData::option('logo_tutelle'),
            'regionLogo' => SiteData::option('logo_region'),
            'regionMention' => SiteData::option('mention_region', __('L’agence Inspire Metz est soutenue financièrement par la Région Grand Est sur le volet économique.', 'inspire-metz')),
        ];
    }
}

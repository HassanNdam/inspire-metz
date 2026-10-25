<?php

namespace App\Options;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Options as Field;

class SiteSettings extends Field
{
    /**
     * The option page menu name.
     *
     * @var string
     */
    public $name = 'Inspire Metz';

    /**
     * The option page menu slug.
     *
     * @var string
     */
    public $slug = 'inspire-metz';

    /**
     * The option page document title.
     *
     * @var string
     */
    public $title = 'Réglages du site Inspire Metz';

    /**
     * The option page permission capability.
     *
     * @var string
     */
    public $capability = 'edit_pages';

    /**
     * The option page menu position.
     *
     * @var int
     */
    public $position = 3;

    /**
     * The option page menu icon.
     *
     * @var string
     */
    public $icon = 'dashicons-admin-site-alt3';

    /**
     * Localized text displayed on the submit button.
     */
    public function updateButton(): string
    {
        return __('Enregistrer', 'inspire-metz');
    }

    /**
     * Localized text displayed after form submission.
     */
    public function updatedMessage(): string
    {
        return __('Réglages enregistrés.', 'inspire-metz');
    }

    /**
     * The option page field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('site_settings', ['title' => 'Réglages', 'style' => 'seamless']);

        $fields
            ->addTab('coordonnees', ['label' => 'Coordonnées', 'placement' => 'left'])
                ->addTextarea('adresse', ['label' => 'Adresse postale', 'rows' => 3, 'new_lines' => 'br'])
                ->addText('telephone', ['label' => 'Téléphone principal'])
                ->addEmail('email', ['label' => 'Email de contact public'])
                ->addRepeater('contacts', ['label' => 'Contacts par service', 'button_label' => 'Ajouter un contact', 'layout' => 'table'])
                    ->addText('service', ['label' => 'Service'])
                    ->addText('telephone', ['label' => 'Téléphone'])
                ->endRepeater()

            ->addTab('reseaux', ['label' => 'Réseaux sociaux'])
                ->addText('hashtag', ['label' => 'Hashtag', 'default_value' => '#INSPIREMETZ'])
                ->addUrl('facebook', ['label' => 'Facebook'])
                ->addUrl('linkedin', ['label' => 'LinkedIn'])
                ->addUrl('x', ['label' => 'X (Twitter)'])
                ->addUrl('instagram', ['label' => 'Instagram'])
                ->addUrl('youtube', ['label' => 'YouTube'])

            ->addTab('missions_tab', ['label' => 'Missions'])
                ->addMessage('missions_info', 'Les trois missions apparaissent sur l’accueil (bloc « Nos missions ») et dans le hero.', ['label' => 'Aide'])
                ->addRepeater('missions', ['label' => 'Missions', 'button_label' => 'Ajouter une mission', 'layout' => 'block', 'max' => 4])
                    ->addSelect('couleur', ['label' => 'Couleur du libellé', 'wrapper' => ['width' => 25]])
                        ->addChoices(['visiter' => 'Vert (Visiter)', 'organiser' => 'Rose (Organiser)', 'investir' => 'Orange (Investir)'])
                    ->addText('libelle', ['label' => 'Libellé', 'instructions' => 'Ex. : VISITER', 'wrapper' => ['width' => 25]])
                    ->addText('titre', ['label' => 'Titre', 'wrapper' => ['width' => 50]])
                    ->addImage('image', ['label' => 'Image', 'return_format' => 'id', 'preview_size' => 'medium', 'wrapper' => ['width' => 30]])
                    ->addTextarea('texte', ['label' => 'Présentation (fenêtre de détail)', 'rows' => 6, 'wrapper' => ['width' => 70]])
                    ->addLink('lien', ['label' => 'Site de la mission'])
                ->endRepeater()
                ->addText('missions_baseline', ['label' => 'Phrase sous les missions', 'default_value' => 'Une équipe aux côtés des acteurs pour faire rayonner l’Eurométropole de Metz.'])

            ->addTab('footer', ['label' => 'Pied de page'])
                ->addText('newsletter_titre', ['label' => 'Titre newsletter', 'default_value' => 'Pour suivre l’actualité de l’Eurométropole de Metz,'])
                ->addText('newsletter_titre_accent', ['label' => 'Titre newsletter (partie en gras)', 'default_value' => 'inscrivez-vous à notre newsletter !'])
                ->addTextarea('newsletter_texte', ['label' => 'Texte newsletter', 'rows' => 3])
                ->addText('mention_agence', ['label' => 'Mention sous le pied de page', 'default_value' => 'L’agence d’attractivité Inspire Metz est au service des 46 communes de l’Eurométropole de Metz.'])
                ->addImage('logo_tutelle', ['label' => 'Logo Euro-Métropole (bandeau blanc)', 'return_format' => 'id', 'wrapper' => ['width' => 30]])
                ->addImage('logo_region', ['label' => 'Logo Région (bandeau blanc)', 'return_format' => 'id', 'wrapper' => ['width' => 30]])
                ->addText('mention_region', ['label' => 'Mention à côté du logo Région', 'default_value' => 'L’agence Inspire Metz est soutenue financièrement par la Région Grand Est sur le volet économique.', 'wrapper' => ['width' => 40]])

            ->addTab('formulaires', ['label' => 'Formulaires'])
                ->addText('notification_contact', ['label' => 'Destinataires des messages de contact', 'instructions' => 'Une ou plusieurs adresses séparées par des virgules. Vide = email de l’administrateur.'])
                ->addText('notification_ambassadeur', ['label' => 'Destinataires des candidatures ambassadeurs', 'instructions' => 'Une ou plusieurs adresses séparées par des virgules.'])
                ->addText('notification_newsletter', ['label' => 'Destinataires des inscriptions newsletter', 'instructions' => 'Laisser vide pour ne pas être notifié (les inscrits restent consultables dans Formulaires).'])

            ->addTab('pages', ['label' => 'Pages de référence'])
                ->addPostObject('page_contact', ['label' => 'Page Contact', 'post_type' => ['page'], 'return_format' => 'id'])
                ->addPostObject('page_newsletter', ['label' => 'Page Newsletters', 'post_type' => ['page'], 'return_format' => 'id'])
                ->addPostObject('page_presse', ['label' => 'Page Espace presse', 'post_type' => ['page'], 'return_format' => 'id'])
                ->addPostObject('page_videos', ['label' => 'Page Vidéos', 'post_type' => ['page'], 'return_format' => 'id'])
                ->addPostObject('page_agence', ['label' => 'Page L’agence', 'post_type' => ['page'], 'return_format' => 'id'])
                ->addPostObject('page_territoire', ['label' => 'Page Découvrir le territoire', 'post_type' => ['page'], 'return_format' => 'id']);

        return $fields->build();
    }
}

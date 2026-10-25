<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

/**
 * Page d'accueil (maquette p. 6) : une section par onglet.
 */
class FrontPage extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('front_page', [
            'title' => 'Page d’accueil',
            'style' => 'seamless',
            'hide_on_screen' => ['the_content'],
        ]);

        $fields->setLocation('page_type', '==', 'front_page');

        $image = ['return_format' => 'id', 'preview_size' => 'medium'];

        $fields
            ->addTab('hero', ['label' => 'Hero', 'placement' => 'left'])
                ->addTextarea('hero_titre', ['label' => 'Titre', 'instructions' => 'Un retour à la ligne = une ligne du titre.', 'rows' => 2, 'new_lines' => '', 'default_value' => "Découvrez\nl’Euro-Métropole de Metz,"])
                ->addTextarea('hero_titre_accent', ['label' => 'Titre (partie en couleur)', 'rows' => 2, 'new_lines' => '', 'default_value' => "territoire innovant\n& durable"])
                ->addGallery('hero_images', ['label' => 'Éclats (3 photos)', 'instructions' => 'Dans l’ordre : Organiser (à gauche), Investir (en bas), Visiter (à droite). Chaque éclat renvoie vers le site de la mission.', 'max' => 3] + $image)

            ->addTab('territoire', ['label' => 'Territoire'])
                ->addText('territoire_titre', ['label' => 'Titre', 'default_value' => 'Un territoire au centre des échanges transfrontaliers et internationaux'])
                ->addWysiwyg('territoire_texte', ['label' => 'Texte', 'toolbar' => 'basic', 'media_upload' => 0])
                ->addLink('territoire_lien', ['label' => 'Bouton'])
                ->addFile('territoire_video', ['label' => 'Vidéo de fond (MP4)', 'instructions' => 'Vidéo muette en boucle, révélée au défilement du hero.', 'return_format' => 'url', 'mime_types' => 'mp4,webm'])
                ->addImage('territoire_image', ['label' => 'Image de remplacement de la vidéo'] + $image)
                ->addImage('territoire_video_image', ['label' => 'Vignette vidéos'] + $image)
                ->addLink('territoire_video_lien', ['label' => 'Lien « toutes nos vidéos »'])

            ->addTab('agence', ['label' => 'L’agence'])
                ->addText('agence_titre', ['label' => 'Titre', 'default_value' => 'L’agence d’attractivité Inspire Metz'])
                ->addWysiwyg('agence_texte', ['label' => 'Texte', 'toolbar' => 'basic', 'media_upload' => 0])
                ->addLink('agence_lien', ['label' => 'Bouton'])
                ->addImage('agence_image', ['label' => 'Photo'] + $image)
                ->addWysiwyg('agence_soutiens', ['label' => 'Texte des soutiens', 'toolbar' => 'basic', 'media_upload' => 0, 'instructions' => 'Le gras est mis en valeur.'])
                ->addRepeater('agence_logos', ['label' => 'Logos des soutiens', 'button_label' => 'Ajouter un logo', 'layout' => 'table', 'max' => 3])
                    ->addImage('logo', ['label' => 'Logo (version blanche)', 'return_format' => 'id', 'preview_size' => 'thumbnail'])
                    ->addUrl('url', ['label' => 'Site'])
                ->endRepeater()

            ->addTab('actualites', ['label' => 'Actualités'])
                ->addText('actualites_titre', ['label' => 'Titre', 'default_value' => 'Nos actualités'])
                ->addNumber('actualites_nombre', ['label' => 'Nombre d’actualités', 'default_value' => 6, 'min' => 3, 'max' => 12])

            ->addTab('presse', ['label' => 'Espace presse'])
                ->addText('presse_titre', ['label' => 'Titre', 'default_value' => 'Retrouvez notre magazine Jeem et nos communiqués dans l’espace Presse'])
                ->addLink('presse_lien', ['label' => 'Bouton'])
                ->addImage('presse_image', ['label' => 'Visuel'] + $image)

            ->addTab('missions', ['label' => 'Missions'])
                ->addText('missions_titre', ['label' => 'Titre', 'default_value' => 'Nos missions'])
                ->addMessage('missions_aide', 'Les missions elles-mêmes se gèrent dans Inspire Metz → Missions.', ['label' => 'Aide'])

            ->addTab('reseaux', ['label' => 'Réseaux sociaux'])
                ->addText('reseaux_titre', ['label' => 'Titre', 'default_value' => 'Suivez-nous sur les réseaux'])
                ->addGallery('reseaux_images', ['label' => 'Photos du bandeau', 'max' => 8] + $image);

        return $fields->build();
    }
}

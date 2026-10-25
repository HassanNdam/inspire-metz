<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

/**
 * Constructeur de page : blocs de contenu empilables (maquette p. 9).
 *
 * Chaque layout correspond à resources/views/blocks/{layout}.blade.php et
 * possède un champ « fond » : les blocs consécutifs de même fond sont
 * regroupés dans une même section (clair = blanc, sombre = bleu nuit).
 */
class ContentBlocks extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('content_blocks', [
            'title' => 'Contenu de la page',
            'menu_order' => 10,
            'style' => 'seamless',
            'hide_on_screen' => ['the_content'],
        ]);

        $fields
            ->setLocation('post_type', '==', 'page')
                ->and('page_type', '!=', 'front_page')
                ->and('page_template', '!=', 'template-contact.blade.php')
            ->or('post_type', '==', 'post');

        $flex = $fields->addFlexibleContent('blocs', ['label' => 'Blocs', 'button_label' => 'Ajouter un bloc']);

        $wysiwyg = ['label' => 'Texte', 'toolbar' => 'full', 'media_upload' => 0, 'tabs' => 'visual'];
        $image = ['return_format' => 'id', 'preview_size' => 'medium'];

        $this->layout($flex, 'texte', 'Texte', fn ($l) => $l
            ->addWysiwyg('texte', $wysiwyg));

        $this->layout($flex, 'texte_image', 'Texte et image', fn ($l) => $l
            ->addText('titre', ['label' => 'Titre'])
            ->addWysiwyg('texte', $wysiwyg)
            ->addLink('lien', ['label' => 'Lien'])
            ->addImage('image', ['label' => 'Image', 'instructions' => 'La légende de l’image (médiathèque) sert de crédit photo.', 'wrapper' => ['width' => 50]] + $image)
            ->addButtonGroup('position', ['label' => 'Position de l’image', 'wrapper' => ['width' => 50]])
                ->addChoices(['gauche' => 'À gauche', 'droite' => 'À droite'])
                ->setDefaultValue('gauche'));

        $this->layout($flex, 'citation', 'Citation', fn ($l) => $l
            ->addTextarea('texte', ['label' => 'Citation', 'rows' => 4]));

        $this->layout($flex, 'chiffres', 'Chiffres clés', fn ($l) => $l
            ->addRepeater('chiffres', ['label' => 'Chiffres', 'button_label' => 'Ajouter un chiffre', 'layout' => 'table', 'max' => 3])
                ->addText('valeur', ['label' => 'Valeur', 'instructions' => 'Ex. : 280 000'])
                ->addText('libelle', ['label' => 'Libellé'])
            ->endRepeater());

        $this->layout($flex, 'liens', 'Liens et fichiers', fn ($l) => $l
            ->addText('titre', ['label' => 'Titre'])
            ->addTextarea('texte', ['label' => 'Texte', 'rows' => 2])
            ->addRepeater('liens', ['label' => 'Liens', 'button_label' => 'Ajouter un lien', 'layout' => 'block'])
                ->addButtonGroup('type', ['label' => 'Type', 'wrapper' => ['width' => 30]])
                    ->addChoices(['lien' => 'Page ou site', 'fichier' => 'Fichier'])
                    ->setDefaultValue('lien')
                ->addLink('lien', ['label' => 'Lien', 'wrapper' => ['width' => 70]])
                    ->conditional('type', '==', 'lien')
                ->addFile('fichier', ['label' => 'Fichier', 'return_format' => 'id', 'wrapper' => ['width' => 35]])
                    ->conditional('type', '==', 'fichier')
                ->addText('libelle', ['label' => 'Libellé du fichier', 'wrapper' => ['width' => 35]])
                    ->conditional('type', '==', 'fichier')
                ->addImage('vignette', ['label' => 'Vignette (facultative)', 'wrapper' => ['width' => 30]] + $image)
                    ->conditional('type', '==', 'fichier')
            ->endRepeater());

        $this->layout($flex, 'galerie', 'Galerie (diaporama)', fn ($l) => $l
            ->addGallery('images', ['label' => 'Images'] + $image));

        $this->layout($flex, 'logos', 'Logos partenaires', fn ($l) => $l
            ->addText('titre', ['label' => 'Titre'])
            ->addLink('lien', ['label' => 'Lien « voir tout »'])
            ->addRepeater('logos', ['label' => 'Logos', 'button_label' => 'Ajouter un logo', 'layout' => 'table'])
                ->addImage('logo', ['label' => 'Logo', 'return_format' => 'id', 'preview_size' => 'thumbnail'])
                ->addUrl('url', ['label' => 'Site'])
            ->endRepeater());

        $this->layout($flex, 'video', 'Vidéo', fn ($l) => $l
            ->addOembed('video', ['label' => 'Vidéo (YouTube, Vimeo…)'])
            ->addImage('poster', ['label' => 'Image de couverture'] + $image));

        $this->layout($flex, 'encart', 'Encart foncé avec bouton', fn ($l) => $l
            ->addText('titre', ['label' => 'Titre'])
            ->addTextarea('texte', ['label' => 'Texte', 'rows' => 4])
            ->addLink('lien', ['label' => 'Bouton'])
            ->addImage('image', ['label' => 'Visuel (facultatif)'] + $image));

        $this->layout($flex, 'contact', 'Coordonnées', fn ($l) => $l
            ->addText('titre', ['label' => 'Titre'])
            ->addTextarea('texte', ['label' => 'Texte', 'rows' => 3])
            ->addText('intitule', ['label' => 'Intitulé du contact', 'wrapper' => ['width' => 50]])
            ->addTextarea('adresse', ['label' => 'Adresse', 'rows' => 3, 'new_lines' => 'br', 'wrapper' => ['width' => 50]])
            ->addText('telephone', ['label' => 'Téléphone', 'wrapper' => ['width' => 50]])
            ->addEmail('email', ['label' => 'Email', 'wrapper' => ['width' => 50]]));

        $this->layout($flex, 'cartes', 'Cartes de navigation', fn ($l) => $l
            ->addRepeater('cartes', ['label' => 'Cartes', 'button_label' => 'Ajouter une carte', 'layout' => 'block', 'max' => 6])
                ->addText('titre', ['label' => 'Titre', 'wrapper' => ['width' => 40]])
                ->addLink('lien', ['label' => 'Lien', 'wrapper' => ['width' => 30]])
                ->addImage('image', ['label' => 'Image', 'preview_size' => 'thumbnail', 'wrapper' => ['width' => 30], 'return_format' => 'id'])
            ->endRepeater(), 'sombre');

        $this->layout($flex, 'colonnes', 'Colonnes avec pictogramme', fn ($l) => $l
            ->addRepeater('colonnes', ['label' => 'Colonnes', 'button_label' => 'Ajouter une colonne', 'layout' => 'block', 'max' => 3])
                ->addImage('picto', ['label' => 'Pictogramme', 'preview_size' => 'thumbnail', 'wrapper' => ['width' => 20], 'return_format' => 'id'])
                ->addText('titre', ['label' => 'Titre', 'wrapper' => ['width' => 80]])
                ->addTextarea('texte', ['label' => 'Texte', 'rows' => 4])
            ->endRepeater()
            ->addLink('lien', ['label' => 'Bouton']), 'sombre');

        $this->layout($flex, 'mosaique', 'Mosaïque de photos', fn ($l) => $l
            ->addGallery('images', ['label' => 'Photos (4 idéalement)', 'max' => 6] + $image), 'sombre');

        $this->layout($flex, 'formulaire', 'Formulaire', fn ($l) => $l
            ->addSelect('formulaire', ['label' => 'Formulaire'])
                ->addChoices(['contact' => 'Contact', 'newsletter' => 'Inscription newsletter', 'ambassadeur' => 'Devenir ambassadeur'])
            ->addText('titre', ['label' => 'Titre'])
            ->addTextarea('texte', ['label' => 'Texte', 'rows' => 3]));

        $flex->endFlexibleContent();

        return $fields->build();
    }

    protected function layout($flex, string $name, string $label, callable $fields, string $background = 'clair'): void
    {
        $layout = $flex->addLayout($name, ['label' => $label]);

        $fields($layout);

        $layout
            ->addButtonGroup('fond', ['label' => 'Fond'])
                ->addChoices(['clair' => 'Clair', 'sombre' => 'Sombre'])
                ->setDefaultValue($background);
    }
}

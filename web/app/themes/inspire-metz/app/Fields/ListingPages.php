<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

/**
 * Titre de liste commun aux pages actualités, presse, newsletters et vidéos.
 * Les éléments listés viennent des types de contenu correspondants.
 */
class ListingPages extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('listing_pages', [
            'title' => 'Liste',
            'menu_order' => 5,
            'hide_on_screen' => ['the_content'],
        ]);

        $fields
            ->setLocation('page_type', '==', 'posts_page')
            ->or('page_template', '==', 'template-presse.blade.php')
            ->or('page_template', '==', 'template-newsletters.blade.php')
            ->or('page_template', '==', 'template-videos.blade.php');

        $fields->addText('liste_titre', [
            'label' => 'Titre de la liste',
            'instructions' => 'Ex. : « Toutes les actualités », « Retrouvez tous les communiqués de presse de l’agence Inspire Metz ».',
        ]);

        return $fields->build();
    }
}

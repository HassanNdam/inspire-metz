<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class PageHeader extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('page_header', [
            'title' => 'En-tête de page',
            'position' => 'acf_after_title',
            'menu_order' => 0,
        ]);

        $fields
            ->setLocation('post_type', '==', 'page')
                ->and('page_type', '!=', 'front_page')
            ->or('post_type', '==', 'post');

        $fields
            ->addTextarea('chapo', [
                'label' => 'Chapô',
                'instructions' => 'Texte d’introduction affiché à droite du titre.',
                'rows' => 3,
            ])
            ->addImage('bandeau', [
                'label' => 'Image bandeau',
                'instructions' => 'Facultative : grande image sous le titre (pages de contenu).',
                'return_format' => 'id',
                'preview_size' => 'medium',
            ]);

        return $fields->build();
    }
}

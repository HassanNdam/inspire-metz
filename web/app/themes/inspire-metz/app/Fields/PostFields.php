<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class PostFields extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('post_fields', ['title' => 'Mise en avant']);

        $fields->setLocation('post_type', '==', 'post');

        $fields
            ->addTrueFalse('a_la_une', ['label' => 'À la une', 'ui' => 1, 'instructions' => 'Affiche l’actualité en tête de la page Actualités.']);

        return $fields->build();
    }
}

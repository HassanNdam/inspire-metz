<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class JeemFields extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('jeem_fields', ['title' => 'Numéro']);

        $fields->setLocation('post_type', '==', 'jeem');

        $fields
            ->addText('numero', ['label' => 'Numéro', 'instructions' => 'Ex. : 17', 'wrapper' => ['width' => 30]])
            ->addText('periode', ['label' => 'Période', 'instructions' => 'Ex. : Mai / Juin 2024', 'wrapper' => ['width' => 70]])
            ->addFile('fichier', ['label' => 'Fichier PDF', 'return_format' => 'id', 'mime_types' => 'pdf'])
            ->addUrl('lien', ['label' => 'Ou lien de lecture en ligne']);

        return $fields->build();
    }
}

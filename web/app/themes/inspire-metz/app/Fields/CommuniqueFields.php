<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class CommuniqueFields extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('communique_fields', ['title' => 'Communiqué']);

        $fields->setLocation('post_type', '==', 'communique');

        $fields
            ->addFile('fichier', ['label' => 'Fichier PDF', 'return_format' => 'id', 'mime_types' => 'pdf'])
            ->addUrl('lien', ['label' => 'Ou lien externe', 'instructions' => 'Utilisé si aucun fichier n’est fourni.']);

        return $fields->build();
    }
}

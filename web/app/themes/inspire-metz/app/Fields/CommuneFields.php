<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class CommuneFields extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('commune_fields', ['title' => 'Commune']);

        $fields->setLocation('post_type', '==', 'commune');

        $fields
            ->addUrl('site', ['label' => 'Site de la commune']);

        return $fields->build();
    }
}

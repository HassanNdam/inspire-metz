<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class LettreInfoFields extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('lettre_info_fields', ['title' => 'Newsletter']);

        $fields->setLocation('post_type', '==', 'lettre_info');

        $fields
            ->addUrl('lien', ['label' => 'Lien vers la newsletter en ligne', 'required' => 1]);

        return $fields->build();
    }
}

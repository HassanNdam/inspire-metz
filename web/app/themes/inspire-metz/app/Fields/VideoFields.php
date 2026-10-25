<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class VideoFields extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('video_fields', ['title' => 'Vidéo']);

        $fields->setLocation('post_type', '==', 'video');

        $fields
            ->addOembed('video', ['label' => 'Vidéo (YouTube, Vimeo…)', 'required' => 1]);

        return $fields->build();
    }
}

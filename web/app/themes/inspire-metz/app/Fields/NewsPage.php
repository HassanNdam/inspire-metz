<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class NewsPage extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('news_page', ['title' => 'Actualités à la une', 'menu_order' => 6]);

        $fields->setLocation('page_type', '==', 'posts_page');

        $fields
            ->addText('une_titre', ['label' => 'Titre', 'default_value' => 'À la une'])
            ->addMessage('une_aide', 'Les actualités affichées ici sont celles cochées « À la une » (2 maximum, les plus récentes).', ['label' => 'Aide']);

        return $fields->build();
    }
}

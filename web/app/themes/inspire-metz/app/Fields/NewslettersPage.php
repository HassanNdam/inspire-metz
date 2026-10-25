<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

/**
 * Page Newsletters (maquette p. 10) : encart d'inscription.
 */
class NewslettersPage extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('newsletters_page', ['title' => 'Encart d’inscription', 'menu_order' => 6]);

        $fields->setLocation('page_template', '==', 'template-newsletters.blade.php');

        $fields
            ->addText('inscription_titre', ['label' => 'Titre', 'default_value' => 'S’inscrire à la newsletter'])
            ->addTextarea('inscription_texte', ['label' => 'Texte', 'rows' => 3])
            ->addImage('inscription_apercu', ['label' => 'Aperçu d’une newsletter', 'return_format' => 'id', 'preview_size' => 'medium']);

        return $fields->build();
    }
}

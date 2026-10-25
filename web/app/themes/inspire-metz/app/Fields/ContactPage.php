<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

/**
 * Page Contact (maquette p. 18).
 */
class ContactPage extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('contact_page', [
            'title' => 'Page contact',
            'menu_order' => 6,
            'hide_on_screen' => ['the_content'],
        ]);

        $fields->setLocation('page_template', '==', 'template-contact.blade.php');

        $fields
            ->addMessage('contact_aide', 'L’adresse et le téléphone viennent de Inspire Metz → Coordonnées. Le texte d’introduction est le chapô.', ['label' => 'Aide'])
            ->addImage('carte', ['label' => 'Plan d’accès', 'return_format' => 'id', 'preview_size' => 'medium'])
            ->addImage('photo', ['label' => 'Photo en bas de page', 'return_format' => 'id', 'preview_size' => 'medium']);

        return $fields->build();
    }
}

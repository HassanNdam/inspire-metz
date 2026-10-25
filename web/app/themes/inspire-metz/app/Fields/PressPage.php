<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

/**
 * Espace presse (maquette p. 14) : derniers Jeem, encart et communiqués.
 */
class PressPage extends Field
{
    /**
     * The field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('press_page', ['title' => 'Espace presse', 'menu_order' => 6]);

        $fields->setLocation('page_template', '==', 'template-presse.blade.php');

        $fields
            ->addText('jeem_titre', ['label' => 'Titre du bloc Jeem', 'default_value' => 'Consultez les derniers Jeem'])
            ->addLink('jeem_lien', ['label' => 'Lien « Voir les anciens numéros »'])
            ->addText('promo_titre', ['label' => 'Encart : titre', 'default_value' => 'Jeem revient avec un nouveau format'])
            ->addTextarea('promo_texte', ['label' => 'Encart : texte', 'rows' => 3])
            ->addLink('promo_lien', ['label' => 'Encart : bouton'])
            ->addImage('promo_image', ['label' => 'Encart : visuel', 'return_format' => 'id', 'preview_size' => 'medium'])
            ->addOembed('promo_video', ['label' => 'Encart : vidéo (facultative)']);

        return $fields->build();
    }
}

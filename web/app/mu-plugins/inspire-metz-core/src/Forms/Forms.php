<?php

namespace InspireMetz\Core\Forms;

/**
 * Déclaration des formulaires du site.
 *
 * Chaque formulaire décrit ses champs (libellé + règles de validation),
 * le type de contenu privé où sont stockées les réponses et la façon
 * d'intituler une réponse dans l'administration.
 */
class Forms
{
    /**
     * @return array<string, array>
     */
    public static function all(): array
    {
        static $forms;

        return $forms ??= apply_filters('inspire_metz/forms', [
            'contact' => [
                'post_type' => 'im_message',
                'label' => __('Messages de contact', 'inspire-metz'),
                'singular' => __('Message de contact', 'inspire-metz'),
                'success' => __('Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.', 'inspire-metz'),
                'title' => fn ($data) => "{$data['nom']} — {$data['email']}",
                'columns' => ['nom', 'email', 'telephone'],
                'fields' => [
                    'nom' => [__('Nom', 'inspire-metz'), 'required|max:120'],
                    'email' => [__('Adresse email', 'inspire-metz'), 'required|email|max:190'],
                    'telephone' => [__('Téléphone fixe', 'inspire-metz'), 'phone|max:30'],
                    'message' => [__('Message', 'inspire-metz'), 'required|max:5000'],
                ],
            ],

            'newsletter' => [
                'post_type' => 'im_inscrit',
                'label' => __('Inscrits newsletter', 'inspire-metz'),
                'singular' => __('Inscription newsletter', 'inspire-metz'),
                'success' => __('Votre abonnement a bien été pris en compte ! Vous recevrez désormais toutes les actualités de l’agence Inspire Metz.', 'inspire-metz'),
                'title' => fn ($data) => "{$data['prenom']} {$data['nom']} — {$data['email']}",
                'columns' => ['nom', 'prenom', 'email', 'code_postal'],
                'unique' => 'email',
                'notify' => false,
                'fields' => [
                    'nom' => [__('Nom', 'inspire-metz'), 'required|max:120'],
                    'prenom' => [__('Prénom', 'inspire-metz'), 'required|max:120'],
                    'code_postal' => [__('Code postal', 'inspire-metz'), 'max:10'],
                    'email' => [__('Adresse mail', 'inspire-metz'), 'required|email|max:190'],
                ],
            ],

            'ambassadeur' => [
                'post_type' => 'im_ambassadeur',
                'label' => __('Candidatures ambassadeurs', 'inspire-metz'),
                'singular' => __('Candidature ambassadeur', 'inspire-metz'),
                'success' => __('Merci ! Votre candidature au Club des Ambassadeurs a bien été envoyée.', 'inspire-metz'),
                'title' => fn ($data) => "{$data['prenom']} {$data['nom']} — {$data['societe']}",
                'columns' => ['nom', 'prenom', 'societe', 'secteur', 'email'],
                'fields' => [
                    'civilite' => [__('Civilité', 'inspire-metz'), 'required|in:madame,monsieur'],
                    'nom' => [__('Nom', 'inspire-metz'), 'required|max:120'],
                    'prenom' => [__('Prénom', 'inspire-metz'), 'required|max:120'],
                    'societe' => [__('Société / Organisme', 'inspire-metz'), 'required|max:190'],
                    'secteur' => [__('Secteur d’activité', 'inspire-metz'), 'required|in:'.implode(',', array_keys(self::sectors()))],
                    'fonction' => [__('Fonction', 'inspire-metz'), 'required|max:190'],
                    'email' => [__('Adresse mail', 'inspire-metz'), 'required|email|max:190'],
                    'adresse' => [__('N° et rue', 'inspire-metz'), 'required|max:255'],
                    'code_postal' => [__('Code postal', 'inspire-metz'), 'required|max:10'],
                    'ville' => [__('Ville', 'inspire-metz'), 'required|max:120'],
                    'pays' => [__('Pays', 'inspire-metz'), 'required|max:120'],
                    'telephone_fixe' => [__('Téléphone fixe', 'inspire-metz'), 'phone|max:30'],
                    'telephone_portable' => [__('Téléphone portable', 'inspire-metz'), 'required|phone|max:30'],
                    'newsletter_club' => [__('Newsletter du Club des Ambassadeurs', 'inspire-metz'), 'boolean'],
                    'infos_partenaires' => [__('Informations de l’agence et de ses partenaires', 'inspire-metz'), 'boolean'],
                    'consentement' => [__('Consentement au traitement des données', 'inspire-metz'), 'accepted'],
                ],
            ],
        ]);
    }

    public static function get(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    public static function byPostType(string $postType): ?array
    {
        foreach (self::all() as $key => $form) {
            if ($form['post_type'] === $postType) {
                return $form + ['key' => $key];
            }
        }

        return null;
    }

    /**
     * Secteurs proposés dans le formulaire ambassadeur.
     *
     * @return array<string, string>
     */
    public static function sectors(): array
    {
        return apply_filters('inspire_metz/ambassador_sectors', [
            'commerce' => __('Commerce', 'inspire-metz'),
            'industrie' => __('Industrie', 'inspire-metz'),
            'services' => __('Services aux entreprises', 'inspire-metz'),
            'numerique' => __('Numérique', 'inspire-metz'),
            'tourisme' => __('Tourisme, hôtellerie, restauration', 'inspire-metz'),
            'sante' => __('Santé', 'inspire-metz'),
            'culture' => __('Culture', 'inspire-metz'),
            'sport' => __('Sport et loisirs', 'inspire-metz'),
            'enseignement' => __('Enseignement, recherche', 'inspire-metz'),
            'public' => __('Secteur public, collectivité', 'inspire-metz'),
            'associatif' => __('Associatif', 'inspire-metz'),
            'autre' => __('Autre', 'inspire-metz'),
        ]);
    }

    /**
     * Valeur lisible d'un champ (booléens, civilité, secteur).
     */
    public static function display(string $field, mixed $value): string
    {
        return match (true) {
            $field === 'secteur' => self::sectors()[$value] ?? (string) $value,
            $field === 'civilite' => ucfirst((string) $value),
            in_array($field, ['newsletter_club', 'infos_partenaires', 'consentement'], true) => $value ? __('Oui', 'inspire-metz') : __('Non', 'inspire-metz'),
            default => (string) $value,
        };
    }
}

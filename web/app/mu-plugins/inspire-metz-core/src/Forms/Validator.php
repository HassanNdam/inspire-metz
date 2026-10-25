<?php

namespace InspireMetz\Core\Forms;

/**
 * Validation minimaliste, règles façon Laravel : « required|email|max:120 ».
 */
class Validator
{
    /**
     * @param  array<string, array{0: string, 1: string}>  $fields
     * @return array{0: array<string, mixed>, 1: array<string, string>} [données nettoyées, erreurs]
     */
    public static function validate(array $fields, array $input): array
    {
        $data = [];
        $errors = [];

        foreach ($fields as $name => [$label, $rules]) {
            $rules = explode('|', $rules);
            $raw = $input[$name] ?? '';

            if (in_array('boolean', $rules, true) || in_array('accepted', $rules, true)) {
                $value = ! empty($raw);
            } else {
                $value = is_string($raw) ? trim(wp_unslash($raw)) : '';
                $value = $name === 'message' ? sanitize_textarea_field($value) : sanitize_text_field($value);
            }

            $data[$name] = $value;

            if ($error = self::check($label, $value, $rules)) {
                $errors[$name] = $error;
            }
        }

        return [$data, $errors];
    }

    protected static function check(string $label, mixed $value, array $rules): ?string
    {
        $empty = $value === '' || $value === false;

        foreach ($rules as $rule) {
            [$rule, $param] = array_pad(explode(':', $rule, 2), 2, null);

            $failed = match ($rule) {
                'required' => $empty,
                'accepted' => $value !== true,
                'email' => ! $empty && ! is_email($value),
                'phone' => ! $empty && ! preg_match('/^[0-9 +().\-]{6,}$/', $value),
                'max' => mb_strlen((string) $value) > (int) $param,
                'in' => ! $empty && ! in_array($value, explode(',', (string) $param), true),
                default => false,
            };

            if ($failed) {
                return match ($rule) {
                    'required' => sprintf(__('Le champ « %s » est obligatoire.', 'inspire-metz'), $label),
                    'accepted' => __('Merci de cocher cette case pour continuer.', 'inspire-metz'),
                    'email' => __('Merci de saisir une adresse email valide.', 'inspire-metz'),
                    'phone' => __('Merci de saisir un numéro de téléphone valide.', 'inspire-metz'),
                    'max' => sprintf(__('%d caractères maximum.', 'inspire-metz'), (int) $param),
                    default => sprintf(__('La valeur du champ « %s » est invalide.', 'inspire-metz'), $label),
                };
            }
        }

        return null;
    }
}

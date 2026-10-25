<?php

/**
 * Plugin Name: Inspire Metz — Cœur
 * Description: Types de contenu et formulaires (contact, newsletter, ambassadeurs) du site Inspire Metz. Indépendant du thème.
 * Version: 0.1.0
 * Author: Pixim
 * Text Domain: inspire-metz
 */

namespace InspireMetz\Core;

defined('ABSPATH') || exit;

require_once __DIR__.'/src/PostTypes.php';
require_once __DIR__.'/src/Mail.php';
require_once __DIR__.'/src/Forms/Forms.php';
require_once __DIR__.'/src/Forms/Validator.php';
require_once __DIR__.'/src/Forms/Handler.php';
require_once __DIR__.'/src/Forms/Admin.php';

PostTypes::boot();
Mail::boot();
Forms\Handler::boot();
Forms\Admin::boot();

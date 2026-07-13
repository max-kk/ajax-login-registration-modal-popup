<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/wp/');
}

if (!defined('SHORTINIT')) {
    define('SHORTINIT', false);
}

if (!defined('LRM_PATH')) {
    define('LRM_PATH', dirname(__DIR__) . '/');
}

if (!defined('LRM_BASENAME')) {
    define('LRM_BASENAME', 'ajax-login-and-registration-modal-popup-dev/ajax-login-registration-modal-popup.php');
}

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/unit/TestCase.php';
require dirname(__DIR__) . '/includes/helpers.php';
require dirname(__DIR__) . '/includes/class-core.php';
require dirname(__DIR__) . '/includes/class-roles-manager.php';

<?php

return [
    'name' => getenv('APP_NAME') ?: 'Eka Developer Cloud',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN),
    'timezone' => getenv('APP_TIMEZONE') ?: 'Europe/Istanbul',
    'locale' => getenv('APP_LOCALE') ?: 'tr',
    'registration_enabled' => filter_var(getenv('APP_REGISTRATION_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN),
];
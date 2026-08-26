<?php

return [
    'url' => rtrim((string) getenv('DOKPLOY_URL'), '/'),
    'api_key' => (string) getenv('DOKPLOY_API_KEY'),
    'server_id' => (string) getenv('DOKPLOY_SERVER_ID'),
    'timeout' => (int) (getenv('DOKPLOY_TIMEOUT') ?: 30),
    'ssl_verify' => filter_var(getenv('DOKPLOY_SSL_VERIFY') ?: 'true', FILTER_VALIDATE_BOOLEAN),
];
<?php

$izinliIpDegeri = trim((string) getenv('EKA_PROVISIONING_ALLOWED_IPS'));

return [
    'api_key' => (string) getenv('EKA_PROVISIONING_API_KEY'),
    'allowed_ips' => $izinliIpDegeri === '' ? [] : array_values(array_filter(array_map('trim', explode(',', $izinliIpDegeri)))),
    'max_body_bytes' => (int) (getenv('EKA_PROVISIONING_MAX_BODY_BYTES') ?: 65536),
];
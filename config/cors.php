<?php

return [
    'paths' => ['api/*', 'filament/*'],
    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://assessment.smkswadaya.sch.id'],

    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,

    'supports_credentials' => true,
];
<?php

return [
    'paths' => ['api/*', 'filament/*'],
    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://demo.assessment.digiexam.web.id'],

    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,

    'supports_credentials' => true,
];

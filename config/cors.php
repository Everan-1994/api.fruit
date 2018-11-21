<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */

    'supportsCredentials'    => false,
    'allowedOrigins'         => [
        'http://127.0.0.1:35920',
        'http://localhost:8080',
        'http://fruit.wei',
    ],
    'allowedOriginsPatterns' => [],
    'allowedHeaders'         => [
        'Origin', 'Content-Type',
        'Cookie', 'X-CSRF-TOKEN',
        'Accept', 'Authorization',
        'X-XSRF-TOKEN'
    ],
    'allowedMethods'         => ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'],
    'exposedHeaders'         => ['Authorization', 'authenticated'],
    'maxAge'                 => 0,

];

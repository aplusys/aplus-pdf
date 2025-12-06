<?php

return [
    'default' => env('PDF_DRIVER', 'wkhtmltopdf'),

    'drivers' => [
        'wkhtmltopdf' => [
            'enabled' => true,
            'binary'  => env('WKHTMLTOPDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
            'timeout' => false,
            'options' => [
                'margin-top'    => 0,
                'margin-right'  => 0,
                'margin-bottom' => 0,
                'margin-left'   => 0,
                'page-size'     => 'a4',
                'orientation'   => 'portrait',
            ],
            'env'     => [],
        ],

        'browsershot' => [
            'node_binary' => env('NODE_BINARY', '/usr/bin/node'),
            'npm_binary' => env('NPM_BINARY', '/usr/bin/npm'),
            'node_modules_path' => env('NODE_MODULES_PATH', base_path('node_modules')),
        ],
    ],

    'image' => [
        'enabled' => true,
        'binary'  => env('WKHTMLTOIMAGE_BINARY', '/usr/local/bin/wkhtmltoimage'),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

];

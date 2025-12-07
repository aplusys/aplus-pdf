<?php

return [
    'default' => env('PDF_DRIVER', 'wkhtmltopdf'),

    'drivers' => [
        'wkhtmltopdf' => [
            'enabled' => true,
            'binary'  => env('WKHTMLTOPDF_BINARY', '/usr/bin/wkhtmltopdf'),
            'timeout' => false,
            'options' => [
                'margin-top'    => 0,
                'margin-right'  => 0,
                'margin-bottom' => 0,
                'margin-left'   => 0,
                'page-size'     => 'a4',
                'orientation'   => 'portrait',
                'enable-local-file-access' => true,
                'disable-smart-shrinking'  => true,
                'dpi'                      => 96,
                'zoom'                     => 1,
            ],
            'env'     => [],
        ],

        'browsershot' => [
            'node_binary' => env('NODE_BINARY', '/usr/bin/node'),
            'npm_binary' => env('NPM_BINARY', '/usr/bin/npm'),
            'node_modules_path' => env('NODE_MODULES_PATH', base_path('node_modules')),
        ],

        'playwright' => [
            'node_binary' => env('NODE_BINARY', '/usr/bin/node'),
            'npm_binary' => env('NPM_BINARY', '/usr/bin/npm'),
            'timeout' => 60,
        ],
    ],

    'image' => [
        'enabled' => true,
        'binary'  => env('WKHTMLTOIMAGE_BINARY', '/usr/bin/wkhtmltoimage'),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

];

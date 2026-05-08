<?php

/**
 * App configuration file.
 */
return [
    'namespace' => 'N8nMarketingTrigger',
    'type' => 'plugin',
    'version' => '1.0.0',
    'author' => '10 Quality Studio <https://10quality.studio>',
    'license' => 'MIT',
    'autoenqueue' => [
        'enabled' => true,
        'priority' => 10,
        'assets' => [
            [
                'id' => 'n8n-mt-campaign-trigger',
                'asset' => 'js/app.js',
                'enqueue' => false,
                'dep' => [ 'jquery' ],
                'footer' => true,
                'is_admin' => true,
            ],
        ],
    ],
    'localize' => [
        'enabled' => false,
        'path' => __DIR__ . '/../../assets/lang/',
        'textdomain' => 'n8n-marketing-trigger',
        'unload' => false,
        'is_public' => false,
    ],
    'paths' => [
        'base' => __DIR__ . '/../',
        'controllers' => __DIR__ . '/../Controllers/',
        'views' => __DIR__ . '/../../assets/views/',
        'lang' => __DIR__ . '/../../assets/lang/',
        'log' => WP_CONTENT_DIR . '/wpmvc/log',
    ],
    'cache' => [
        'enabled' => true,
        'storage' => 'auto',
        'path' => WP_CONTENT_DIR . '/wpmvc/cache',
        'securityKey' => '',
        'fallback' => [
            'memcache' => 'files',
            'apc' => 'sqlite',
        ],
        'htaccess' => true,
        'server' => [
            [ '127.0.0.1', 11211, 1 ],
        ],
    ],
    'addons' => [
        'WPMVC\\Addons\\Administrator\\AdministratorAddon',
        'WPMVC\\Addons\\Metaboxer\\MetaboxerAddon',
    ],
    'administrator_models' => [
        'n8n_mt_settings' => 'N8nMarketingTrigger\Models\Settings',
    ],
    'metaboxer_models' => [
        'marketing_campaign' => 'N8nMarketingTrigger\Models\Campaign',
    ],
];

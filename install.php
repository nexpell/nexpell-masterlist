<?php

if (!function_exists('safe_query')) {
    die('Access denied');
}

global $plugin;

PluginInstallerHelper::install([

    'modulname'  => 'masterlist',
    'name'       => 'Masterlist',
    'version'    => (string)($plugin['version'] ?? '0.0.0'),
    'author'     => 'T-Seven',
    'website'    => 'https://www.nexpell.de',
    'path'       => 'includes/plugins/masterlist/',

    'admin_file' => '',
    'index_link' => 'masterlist',
    'sidebar'    => 'deactivated',

    'languages' => [
        'plugin_info_masterlist' => [
            'de' => 'Mit diesem Plugin könnt ihr eure Masterlist mit Slider und Seite anzeigen lassen.',
            'en' => 'With this plugin you can display your masterlist with slider and page.',
            'it' => 'Con questo plugin puoi visualizzare la tua masterlist con slider e pagina.'
        ]
    ],

    'permissions' => [
        'masterlist'
    ],

    'website_navigation' => [
        [
            'url'        => 'index.php?site=masterlist',
            'mnavID'     => 6,
            'sort'       => 1,
            'indropdown' => 1,
            'labels' => [
                'de' => 'Masterliste',
                'en' => 'Game Masterlist',
                'it' => 'Lista giochi'
            ]
        ]
    ]

]);

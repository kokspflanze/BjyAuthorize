<?php

return [
    'modules' => [
        'LmcUser',
        'BjyAuthorize'
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            __DIR__ . '/testing.config.php',
        ],
        'module_paths' => [],
    ],
];

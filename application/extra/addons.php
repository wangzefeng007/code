<?php

return [
    'autoload' => false,
    'hooks' => [
        'module_init' => [
            'csmding',
        ],
        'admin_login_after' => [
            'csmding',
        ],
        'ems_send' => [
            'faems',
        ],
        'ems_notice' => [
            'faems',
        ],
    ],
    'route' => [],
    'priority' => [],
];

<?php

namespace BjyAuthorize;

return [
    'bjyauthorize' => [
        // default role for unauthenticated users
        'default_role'          => 'guest',

        'role_providers' => [
            'foo',
        ],
        'rule_providers'        => [
            'allow' => [
            ],
            'deny' => [
            ],
        ],
    ],
];

<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize;

return [
    'bjyauthorize' => [
        // default role for unauthenticated users
        'default_role'          => 'guest',

        // default role for authenticated users (if using the
        // 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider' identity provider)
        'authenticated_role'    => 'user',

        // identity provider service name
        'identity_provider'     => 'BjyAuthorize\Provider\Identity\LmcUserLaminasDb',

        // Role providers to be used to load all available roles into Laminas\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'role_providers'        => [],

        // Resource providers to be used to load all available resources into Laminas\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'resource_providers'    => [],

        // Rule providers to be used to load all available rules into Laminas\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'rule_providers'        => [],

        // Guard listeners to be attached to the application event manager
        'guards'                => [],

        // strategy service name for the strategy listener to be used when permission-related errors are detected
        'unauthorized_strategy' => 'BjyAuthorize\View\UnauthorizedStrategy',

        // Template name for the unauthorized strategy
        'template'              => 'error/403',

        // Flag if cache should be enabled or not
        'cache_enabled'         => true,

        // cache options have to be compatible with Laminas\Cache\StorageFactory::factory
        'cache_options'         => [
            'adapter'   => [
                'name' => 'memory',
            ],
            'plugins'   => [
                'serializer',
            ]
        ],

        // Key used by the cache for caching the acl
        'cache_key'             => 'bjyauthorize_acl'
    ],
    'service_manager' => [
        'factories' => [
            'BjyAuthorize\Cache' => Service\CacheFactory::class,
            'BjyAuthorize\CacheKeyGenerator' => Service\CacheKeyGeneratorFactory::class,
            'BjyAuthorize\Config' => Service\ConfigServiceFactory::class,
            'BjyAuthorize\Guards' => Service\GuardsServiceFactory::class,
            'BjyAuthorize\RoleProviders' => Service\RoleProvidersServiceFactory::class,
            'BjyAuthorize\ResourceProviders' => Service\ResourceProvidersServiceFactory::class,
            'BjyAuthorize\RuleProviders' => Service\RuleProvidersServiceFactory::class,
            'BjyAuthorize\Service\RoleDbTableGateway' => Service\UserRoleServiceFactory::class,
            Collector\RoleCollector::class => Service\RoleCollectorServiceFactory::class,
            Guard\Controller::class => Service\ControllerGuardServiceFactory::class,
            Guard\Route::class => Service\RouteGuardServiceFactory::class,
            Provider\Identity\AuthenticationIdentityProvider::class
                => Service\AuthenticationIdentityProviderServiceFactory::class,
            Provider\Identity\LmcUserLaminasDb::class => Service\LmcUserLaminasDbIdentityProviderServiceFactory::class,
            Provider\Identity\ProviderInterface::class => Service\IdentityProviderServiceFactory::class,
            Provider\Resource\Config::class => Service\ConfigResourceProviderServiceFactory::class,
            Provider\Role\Config::class => Service\ConfigRoleProviderServiceFactory::class,
            Provider\Role\LaminasDb::class => Service\LaminasDbRoleProviderServiceFactory::class,
            Provider\Role\ObjectRepositoryProvider::class => Service\ObjectRepositoryRoleProviderFactory::class,
            Provider\Rule\Config::class => Service\ConfigRuleProviderServiceFactory::class,
            Service\Authorize::class => Service\AuthorizeFactory::class,
            View\UnauthorizedStrategy::class => Service\UnauthorizedStrategyServiceFactory::class,
        ],
        'invokables'  => [
            View\RedirectionStrategy::class,
        ],
        'aliases'     => [
            'bjyauthorize_zend_db_adapter' => \Laminas\Db\Adapter\Adapter::class,
        ],
        'initializers' => [
            Service\AuthorizeAwareServiceInitializer::class
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'error/403' => __DIR__ . '/../view/error/403.phtml',
            'laminas-developer-tools/toolbar/bjy-authorize-role'
                => __DIR__ . '/../view/laminas-developer-tools/toolbar/bjy-authorize-role.phtml',
        ],
    ],
    'laminas-developer-tools' => [
        'profiler' => [
            'collectors' => [
                'bjy_authorize_role_collector' => 'BjyAuthorize\\Collector\\RoleCollector',
            ],
        ],
        'toolbar' => [
            'entries' => [
                'bjy_authorize_role_collector' => 'laminas-developer-tools/toolbar/bjy-authorize-role',
            ],
        ],
    ],
];

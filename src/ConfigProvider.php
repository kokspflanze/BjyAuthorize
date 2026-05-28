<?php

declare(strict_types=1);

namespace BjyAuthorize;

use BjyAuthorize\View\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'view_helpers' => [
                'aliases' => [
                    'isGranted' => Helper\IsGranted::class,
                    'identity' => Helper\Identity::class,
                ],
                'factories' => [
                    Helper\IsGranted::class => Helper\IsGrantedFactory::class,
                    Helper\Identity::class => Helper\IdentityFactory::class,
                ],
            ],
            'bjyauthorize' => [
                // default role for unauthenticated users
                'default_role'          => 'guest',

                'role_providers' => [
                    'type' => IdentityRoleInterface::TYPE_DOCTRINE,
                    'options' => [
                        'role_entity_class' => IdentityRoleInterface::class,
                        'object_manager' => EntityManagerInterface::class,
                    ],
                ],
                'rule_providers'        => [
                    'allow' => [
                    ],
                    'deny' => [
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
            ],
            'factories'  => [
                AuthenticationMiddleware::class => AuthenticationFactory::class,
                Authorization::class => AuthorizationFactory::class,
                AclInterface::class => AclFactory::class,
                AuthorizationContext::class => InvokableFactory::class,
            ],
        ];
    }

}

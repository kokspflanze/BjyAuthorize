<?php

namespace BjyAuthorize\Service;

/**
 * Factory responsible of a set of {@see \BjyAuthorize\Provider\Role\ProviderInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RoleProvidersServiceFactory extends BaseProvidersServiceFactory
{
    const PROVIDER_SETTING = 'role_providers';
}

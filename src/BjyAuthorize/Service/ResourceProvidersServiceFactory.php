<?php

namespace BjyAuthorize\Service;

/**
 * Factory responsible of a set of {@see \BjyAuthorize\Provider\Resource\ProviderInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ResourceProvidersServiceFactory extends BaseProvidersServiceFactory
{
    const PROVIDER_SETTING = 'resource_providers';
}

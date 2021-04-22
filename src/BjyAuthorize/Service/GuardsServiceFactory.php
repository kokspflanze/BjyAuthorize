<?php

namespace BjyAuthorize\Service;

/**
 * Factory responsible of building a set of {@see \BjyAuthorize\Guard\GuardInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class GuardsServiceFactory extends BaseProvidersServiceFactory
{
    const PROVIDER_SETTING = 'guards';
}

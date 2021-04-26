<?php

namespace BjyAuthorize\Service;

/**
 * Factory responsible of a set of {@see \BjyAuthorize\Provider\Rule\ProviderInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RuleProvidersServiceFactory extends BaseProvidersServiceFactory
{
    const PROVIDER_SETTING = 'rule_providers';
}

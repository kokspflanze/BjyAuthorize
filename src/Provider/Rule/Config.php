<?php

namespace BjyAuthorize\Provider\Rule;

/**
 * Rule provider based on a given array of rules
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Config implements ProviderInterface
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->rules = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getRules()
    {
        return $this->rules;
    }
}

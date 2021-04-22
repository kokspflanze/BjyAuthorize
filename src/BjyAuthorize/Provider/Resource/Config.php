<?php

namespace BjyAuthorize\Provider\Resource;

/**
 * Array-based resources list
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Config implements ProviderInterface
{
    /**
     * @var \Laminas\Permissions\Acl\Resource\ResourceInterface[]
     */
    protected $resources = [];

    /**
     * @param \Laminas\Permissions\Acl\Resource\ResourceInterface[] $config
     */
    public function __construct(array $config = [])
    {
        $this->resources = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getResources()
    {
        return $this->resources;
    }
}

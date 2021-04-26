<?php

namespace BjyAuthorize\Provider\Resource;

/**
 * Resource provider interface, provides existing resources list
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @return \Laminas\Permissions\Acl\Resource\ResourceInterface[]
     */
    public function getResources();
}

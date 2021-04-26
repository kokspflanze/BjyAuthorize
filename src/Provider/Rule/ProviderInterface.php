<?php

namespace BjyAuthorize\Provider\Rule;

/**
 * Rule provider interface, allows specifying that an object
 * can provide ACL rules
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @return array
     */
    public function getRules();
}

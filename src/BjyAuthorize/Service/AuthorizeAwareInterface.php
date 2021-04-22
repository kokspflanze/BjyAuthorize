<?php

namespace BjyAuthorize\Service;

/**
 * Interface for Authorize-aware objects. Allows injection
 * of an {@see \BjyAuthorize\Service\Authorize}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
interface AuthorizeAwareInterface
{
    /**
     * @param \BjyAuthorize\Service\Authorize $auth
     *
     * @return void
     */
    public function setAuthorizeService(Authorize $auth);
}

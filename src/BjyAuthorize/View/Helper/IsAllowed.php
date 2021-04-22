<?php

namespace BjyAuthorize\View\Helper;

use BjyAuthorize\Service\Authorize;
use Laminas\View\Helper\AbstractHelper;

/**
 * IsAllowed View helper. Allows checking access to a resource/privilege in views.
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class IsAllowed extends AbstractHelper
{
    /**
     * @var Authorize
     */
    protected $authorizeService;

    /**
     * @param Authorize $authorizeService
     */
    public function __construct(Authorize $authorizeService)
    {
        $this->authorizeService = $authorizeService;
    }

    /**
     * @param mixed $resource
     * @param mixed|null $privilege
     *
     * @return bool
     */
    public function __invoke($resource, $privilege = null)
    {
        return $this->authorizeService->isAllowed($resource, $privilege);
    }
}

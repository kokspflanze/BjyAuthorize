<?php
declare(strict_types=1);

namespace BjyAuthorize\View\Helper;

use BjyAuthorize\AuthorizationContext;
use Laminas\View\Helper\AbstractHelper;
use Mezzio\Authentication\UserInterface;

class Identity extends AbstractHelper
{
    protected AuthorizationContext $context;

    /**
     * @param AuthorizationContext $context
     */
    public function __construct(AuthorizationContext $context)
    {
        $this->context = $context;
    }


    public function __invoke(): ?UserInterface
    {
        return $this->context->getUser();
    }
}
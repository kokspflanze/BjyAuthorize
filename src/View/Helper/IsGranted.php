<?php
declare(strict_types=1);

namespace BjyAuthorize\View\Helper;

use BjyAuthorize\AuthorizationContext;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\View\Helper\AbstractHelper;

class IsGranted extends AbstractHelper
{
    protected AclInterface $acl;

    protected AuthorizationContext $context;

    /**
     * @param AclInterface $acl
     * @param AuthorizationContext $context
     */
    public function __construct(AclInterface $acl, AuthorizationContext $context)
    {
        $this->acl = $acl;
        $this->context = $context;
    }


    public function __invoke(string $resource): bool
    {
        $allowed = false;
        $user = $this->context->getUser();

        if ($user !== null) {
            foreach ($user->getRoles() as $role) {
                if ($this->acl->isAllowed($role, $resource)) {
                    $allowed = true;
                    break;
                }
            }
        }

        return $allowed;
    }
}
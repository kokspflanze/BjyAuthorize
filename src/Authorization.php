<?php

declare(strict_types=1);

namespace BjyAuthorize;

use Laminas\Permissions\Acl\AclInterface;
use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class Authorization implements AuthorizationInterface
{
    protected AclInterface $acl;

    protected $ignoreRoutes = [];

    public function __construct(AclInterface $acl, array $ignoreRoutes)
    {
        $this->acl = $acl;
        $this->ignoreRoutes = $ignoreRoutes;
    }

    public function isGranted($role, ServerRequestInterface $request): bool
    {
        $routeResult = $request->getAttribute(RouteResult::class, false);

        if (false === $routeResult) {
            throw new RuntimeException(sprintf(
                'The %s attribute is missing in the request; cannot perform ACL authorization checks',
                RouteResult::class
            ));
        }

        $routeName = $routeResult->getMatchedRouteName();

        if (in_array($routeName, $this->ignoreRoutes, true)) {
            return true;
        }

        if (false === $this->acl->hasResource($routeName)) {
            throw new RuntimeException(sprintf(
                'Routename "%s" not found in resource',
                $routeName
            ));
        }

        return $this->acl->isAllowed($role, $routeName);
    }

}
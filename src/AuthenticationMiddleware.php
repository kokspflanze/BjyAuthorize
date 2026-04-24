<?php

declare(strict_types=1);

namespace BjyAuthorize;

use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\UserInterface;
use Mezzio\Authorization\AuthorizationInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    protected AuthenticationInterface $auth;

    protected AuthorizationInterface $acl;

    public function __construct(AuthenticationInterface $auth, AuthorizationInterface $acl)
    {
        $this->auth = $auth;
        $this->acl = $acl;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->auth->authenticate($request);

        $roleList = ['guest'];
        if (null !== $user) {
            $request = $request->withAttribute(UserInterface::class, $user);
            $roleList = $user->getRoles();
        }

        $access = false;
        foreach ($roleList as $role) {
            if ($this->acl->isGranted($role, $request)) {
                $access = true;
                break;
            }
        }


        if (false === $access) {
            return $this->auth->unauthorizedResponse($request);
        }

        return $handler->handle($request);
    }


}
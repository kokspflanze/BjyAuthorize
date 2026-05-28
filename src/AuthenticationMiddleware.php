<?php

declare(strict_types=1);

namespace BjyAuthorize;

use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\UserInterface;
use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    protected AuthenticationInterface $auth;

    protected AuthorizationInterface $acl;

    protected AuthorizationContext $context;

    /**
     * @param AuthenticationInterface $auth
     * @param AuthorizationInterface $acl
     * @param AuthorizationContext $context
     */
    public function __construct(AuthenticationInterface $auth, AuthorizationInterface $acl, AuthorizationContext $context)
    {
        $this->auth = $auth;
        $this->acl = $acl;
        $this->context = $context;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if (! $session instanceof SessionInterface) {
            throw Exception\MissingSessionContainerException::create();
        }

        $user = null;

        if ($session->has(UserInterface::class)) {
            $user = $this->auth->authenticate($request);
        }

        $roleList = ['guest'];
        if (null !== $user) {
            $request = $request->withAttribute(UserInterface::class, $user);
            $this->context->setUser($user);
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
<?php
declare(strict_types=1);

namespace BjyAuthorize;

use Mezzio\Authentication\UserInterface;

class AuthorizationContext
{
    protected ?UserInterface $user = null;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): AuthorizationContext
    {
        $this->user = $user;
        return $this;
    }


}
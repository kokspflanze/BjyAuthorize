<?php
declare(strict_types=1);

namespace BjyAuthorize;

interface IdentityRoleInterface
{
    const TYPE_DOCTRINE = 'DOCTRINE';

    public function getId(): string;

    public function getParentId(): string;

    public function getRoleId(): string;
}
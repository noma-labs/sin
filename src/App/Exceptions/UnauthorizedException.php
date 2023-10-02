<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException
{
    private $requiredRoles = [];

    private $requiredPermissions = [];

    public static function forRoles(array $roles): self
    {
        $message = 'User does not have the right roles.';
        if (config('permission.display_permission_in_exception')) {
            $permStr = implode(', ', $roles);
            $message = 'User does not have the right roles. Necessary roles are '.$permStr;
        }
        $exception = new self(401, $message, null, []);
        $exception->requiredRoles = $roles;

        return $exception;
    }

    public static function forAbilities(array $abilities): self
    {
        $message = 'User does not have the right permissions.';
        if (config('permission.display_permission_in_exception')) {
            $permStr = implode(', ', $abilities);
            $message = 'User does not have the right permissions. Necessary permissions are '.$permStr;
        }
        $exception = new self(401, $message, null, []);
        $exception->requiredPermissions = $abilities;

        return $exception;
    }

    public static function notLoggedIn(): self
    {
        return new self(403, 'User is not logged in.', null, []);
    }

    public function getRequiredRoles(): array
    {
        return $this->requiredRoles;
    }

    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }
}

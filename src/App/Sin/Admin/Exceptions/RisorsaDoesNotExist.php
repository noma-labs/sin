<?php

namespace App\Admin\Exceptions;

use InvalidArgumentException;

class RisorsaDoesNotExist extends InvalidArgumentException
{
    public static function create(string $permissionName): RisorsaDoesNotExist
    {
        return new self("There is no permission named `{$permissionName}` ");
    }

    public static function withId(int $permissionId): RisorsaDoesNotExist
    {
        return new self("There is no [permission] with id `{$permissionId}`.");
    }
}

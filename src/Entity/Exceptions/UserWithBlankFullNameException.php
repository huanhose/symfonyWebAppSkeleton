<?php

namespace App\Entity\Exceptions;

use Exception;

final class UserWithBlankFullNameException extends Exception
{
    public static function create(): self
    {
        return new static("A user can't have a empy full name");
    }
}

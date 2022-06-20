<?php

namespace App\Entity\Exceptions;

use Exception;

final class UserWithBlankNameException extends Exception
{
    public static function create()
    {
        return new static("A user can't have a empy name");
    }
}

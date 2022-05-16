<?php
namespace App\Entity\Exceptions;

use Exception;

class UserWithBlankFullNameException extends Exception
{
    public static function create()
    {
        return new static("A user can't have a empy full name");
    }
}

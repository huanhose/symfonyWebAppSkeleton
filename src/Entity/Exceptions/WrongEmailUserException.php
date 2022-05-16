<?php
namespace App\Entity\Exceptions;

use Exception;

class WrongEmailUserException extends Exception
{
    public static function onValue(string $email)
    {
        return new static("$email isn't a valida email for a user");
    }
}

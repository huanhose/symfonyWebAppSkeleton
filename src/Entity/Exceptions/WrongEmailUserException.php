<?php

namespace App\Entity\Exceptions;

use Exception;

final class WrongEmailUserException extends Exception
{
    public static function onValue(string $email): self
    {
        return new static("$email isn't a valida email for a user");
    }
}

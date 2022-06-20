<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Entity\User;

class AfterCreateUserEvent extends Event
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}

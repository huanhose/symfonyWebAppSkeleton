<?php

namespace App\Tests\Unitary\User;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateNewInstance(): void
    {
        $user = new User();
        $this->assertNotNull($user);
    }

    public function testCreateNewUser(): void
    {
        //Create and set new user
        $user = new User();
        $user
            ->setEmail('foo@me.com')
            ->setName('foo')
            ->setFullName('John Smith');

        //Check users data
        $this->assertEquals('foo@me.com', $user->getEmail());
        $this->assertEquals('foo', $user->getName());
        $this->assertEquals('John Smith', $user->getFullName());
        
        //A new user has ROLE_USER as default role
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }   

}


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

    public function testAddRole()
    {
        $user = new User();
        $newRole = 'ROLE_FOO';
        $user->addRole($newRole);
        $this->assertContains($newRole, $user->getRoles());
    }

    public function testDeleteRole()
    {
        $user = new User();
        $newRole = 'ROLE_FOO';
        $user->addRole($newRole);
        $this->assertContains($newRole, $user->getRoles());

        $user->deleteRole($newRole);
        $this->assertNotContains($newRole, $user->getRoles());
    }

    /**
     * Get App roles return only App roles
     *
     * @return void
     */
    public function testGetAppRole()
    {
        $user = new User();
        $newAppRole = 'ROLE_ADMIN';

        //Method getAppRoles must return only ROLE_ADMIN and not ROLE_USER(default role)
        $user->addAppRole($newAppRole);
        $this->assertEquals([$newAppRole], $user->getAppRoles());
    }

    public function testSetAppRoles()
    {
        $user = new User();
        $listNewAppRoles = ['ROLE_ADMIN'];

        //We set an entire list of app roles
        $user->setAppRoles($listNewAppRoles);
        $this->assertEquals($listNewAppRoles, $user->getAppRoles());
    }

    public function testDeleteAppRole()
    {
        $user = new User();
        $newAppRole = 'ROLE_ADMIN';

        $user->addAppRole($newAppRole);
        $this->assertContains($newAppRole, $user->getAppRoles());

        $user->deleteAppRole($newAppRole);
        $this->assertNotContains($newAppRole, $user->getAppRoles());
    }

    public function testIsVerifiedAddRole()
    {
        $user = new User();
        $role_verified = 'ROLE_VERIFIED_USER';

        //Initialy, user has not this role
        $this->assertNotContains($role_verified, $user->getRoles());
        //When verified, he adquires the role
        $user->setIsVerified(true);
        $this->assertContains($role_verified, $user->getRoles());
        //Finally, when is not verified, he loses the role
        $user->setIsVerified(false);
        $this->assertNotContains($role_verified, $user->getRoles());
    }


}


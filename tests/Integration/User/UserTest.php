<?php
namespace App\Tests\Integration\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface; 

class UserTest extends KernelTestCase
{
    public function testCreateNewUserWithoutErrors()
    {
        $user = new User();
        $user
            ->setEmail('foo@me.com')
            ->setName('foo')
            ->setFullName('John Smith')
            ->setPassword('123456');

        $entityManager = $this->getORMEntityManager();
        $entityManager->persist($user);
        $entityManager->flush(); 

        $this->assertTrue(true);
    }

    private function getORMEntityManager(): EntityManagerInterface
    {
        $container = static::getContainer();
        $entityManager = $container->get('Doctrine\ORM\EntityManagerInterface');
        return $entityManager;
    }
}



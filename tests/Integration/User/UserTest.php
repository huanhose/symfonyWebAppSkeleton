<?php

namespace App\Tests\Integration\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Test User Entity with persistence layer
 */
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

        $userRepository = $this->getRepository();
        $persistedUser = $userRepository->find($user->getId());

        $this->assertEquals($user->getEmail(), $persistedUser->getEmail());
        $this->assertEquals($user->getName(), $persistedUser->getName());
        $this->assertEquals($user->getFullName(), $persistedUser->getFullName());
    }

    public function testFindByEmail()
    {
        //We create a user
        $user = new User();
        $user
            ->setEmail('foo@me.com')
            ->setName('foo')
            ->setFullName('John Smith')
            ->setPassword('123456');

        $entityManager = $this->getORMEntityManager();
        $entityManager->persist($user);
        $entityManager->flush();

        //We get user created, by email
        $userRepository = $this->getRepository();
        $userFound = $userRepository->findByEmail('foo@me.com');
        $this->assertNotNull($userFound);
        $this->assertEquals($user->getEmail(), $userFound->getEmail());
    }

    /**
     * Get an Doctrine Entity Manager
     *
     * @return EntityManagerInterface
     */
    private function getORMEntityManager(): EntityManagerInterface
    {
        $container = static::getContainer();
        $entityManager = $container->get('Doctrine\ORM\EntityManagerInterface');
        return $entityManager;
    }

    /**
     * Get a User repository instance
     *
     * @return UserRepository
     */
    private function getRepository(): UserRepository
    {
        $container = static::getContainer();
        $repository = $container->get('App\Repository\UserRepository');
        return $repository;
    }
}

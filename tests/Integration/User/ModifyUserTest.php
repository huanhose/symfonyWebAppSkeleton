<?php

namespace App\Tests\Integration\User;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\CreateUser;
use App\Service\User\CreateUserDTO;
use App\Service\User\ModifyUser;
use App\Service\User\ModifyUserDTO;

/**
 * Test ModifyUser Service  use case
 */
class ModifyUserTest extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();
    }

    public function testModifyUser()
    {
        //Setup
        $user = $this->getCreateUserService()(new CreateUserDTO(
            email: 'pepe@zulu.com',
            name: 'pepe',
            fullName: 'Pepe Luis',
            password: '123456'
        ));

        $userData = new ModifyUserDTO(
            id: $user->getId(),
            email: 'luis@zulu.com',
            name: 'Luis',
            fullName: 'Luis Pepe',
        );
        $modifyUserService = $this->getModifyUserService();
        $modifiedUser = $modifyUserService($userData);

        $this->assertNotNull($modifiedUser);

        //Check data user modified
        $this->assertEquals($userData->email, $modifiedUser->getEmail());
        $this->assertEquals($userData->name, $modifiedUser->getName());
        $this->assertEquals($userData->fullName, $modifiedUser->getFullName());

        //Check user persisted in db
        $persistedUser = $this->getUserById($modifiedUser->getId());
        $this->assertEquals($userData->email, $persistedUser->getEmail());
        $this->assertEquals($userData->name, $persistedUser->getName());
        $this->assertEquals($userData->fullName, $persistedUser->getFullName());
    }

    public function testModifyOnlyEmail()
    {
        //Setup
        $user = $this->getCreateUserService()(new CreateUserDTO(
            email: 'pepe@zulu.com',
            name: 'pepe',
            fullName: 'Pepe Luis',
            password: '123456'
        ));

        //Data to be modified, only email
        $userData = new ModifyUserDTO(
            id: $user->getId(),
            email: 'luis@zulu.com'
        );
        $modifyUserService = $this->getModifyUserService();
        $modifiedUser = $modifyUserService($userData);

        //Check data user modified
        $this->assertEquals($userData->email, $modifiedUser->getEmail());

        //Check non modified data
        $this->assertEquals($user->getName(), $modifiedUser->getName());
        $this->assertEquals($user->getFullName(), $modifiedUser->getFullName());
    }

    public function testModifyOnlyName()
    {
        //Setup
        $user = $this->getCreateUserService()(new CreateUserDTO(
            email: 'pepe@zulu.com',
            name: 'pepe',
            fullName: 'Pepe Luis',
            password: '123456'
        ));

        //Data to be modified, only name
        $userData = new ModifyUserDTO(
            id: $user->getId(),
            name: 'FooName'
        );
        $modifyUserService = $this->getModifyUserService();
        $modifiedUser = $modifyUserService($userData);

        //Check data user modified
        $this->assertEquals($userData->name, $modifiedUser->getName());

        //Check non modified data
        $this->assertEquals($user->getEmail(), $modifiedUser->getEmail());
        $this->assertEquals($user->getFullName(), $modifiedUser->getFullName());
    }

    public function testModifyOnlyFullName()
    {
        //Setup
        $user = $this->getCreateUserService()(new CreateUserDTO(
            email: 'pepe@zulu.com',
            name: 'pepe',
            fullName: 'Pepe Luis',
            password: '123456'
        ));

        //Data to be modified, only Full name
        $userData = new ModifyUserDTO(
            id: $user->getId(),
            fullName: 'Foo Mee'
        );
        $modifyUserService = $this->getModifyUserService();
        $modifiedUser = $modifyUserService($userData);

        //Check data user modified
        $this->assertEquals($userData->fullName, $modifiedUser->getFullName());

        //Check non modified data
        $this->assertEquals($user->getEmail(), $modifiedUser->getEmail());
        $this->assertEquals($user->getName(), $modifiedUser->getName());
    }

    public function testModifyNotEmail()
    {
        //Setup
        $user = $this->getCreateUserService()(new CreateUserDTO(
            email: 'pepe@zulu.com',
            name: 'pepe',
            fullName: 'Pepe Luis',
            password: '123456'
        ));

        //Data to be modified, only Full name
        $userData = new ModifyUserDTO(
            id: $user->getId(),
            name: 'Foo',
            fullName: 'Foo Mee'
        );
        $modifyUserService = $this->getModifyUserService();
        $modifiedUser = $modifyUserService($userData);

        //Check data user modified
        $this->assertEquals($userData->name, $modifiedUser->getName());
        $this->assertEquals($userData->fullName, $modifiedUser->getFullName());

        //Check non modified data
        $this->assertEquals($user->getEmail(), $modifiedUser->getEmail());
    }


    /**
     * Get an instance of ModifyUser service
     *
     * @return ModifyUser
     */
    private function getModifyUserService(): ModifyUser
    {
        $container = static::getContainer();
        $entityManager = $container->get('Doctrine\ORM\EntityManagerInterface');
        $userRepository = $container->get(UserRepository::class);

        return new ModifyUser($entityManager, $userRepository);
    }

    /**
     * Get an user by their id
     *
     * @param integer $id
     * @return User
     */
    private function getUserById(int $id): User
    {
        $container = static::getContainer();

        $userRepository = $container->get(UserRepository::class);
        return $userRepository->find($id);
    }

    /**
     * Get an instance of CreateUser service
     *
     * @return CreateUser
     */
    private function getCreateUserService(?object $emailVerifierMock = null): CreateUser
    {
        $container = static::getContainer();

        //We get dependencies
        $entityManager = $container->get('Doctrine\ORM\EntityManagerInterface');
        $userPasswordHasher = $container->get('Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface');
        $eventDispatcher = $container->get('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $createUserService = new CreateUser(
            $entityManager,
            $userPasswordHasher,
            $eventDispatcher
        );

        return $createUserService;
    }
}

<?php
namespace App\Tests\Integration\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\User\CreateUser;
use App\Service\User\CreateUserDTO;

/**
 * Test the service Use Case CreateUser
 * CreateUser service is similar to Register User. To  create new users in backoffice. We don't send verifier email 
 */
class CreateUserTest extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();
    }

    /**
     * Test Create a new user
     * Normal case
     * 
     * @return void
     */
    public function testCreateUser()
    {
        //Get an instance of registerUser instance
        $createUserService = $this->getCreateUserService();

        //Call service
        $userData = new CreateUserDTO(
            email: 'zulu@miemail.com',
            name: 'zulu',
            fullName: 'Zulu ambigu',
            password: '123456'
        );
        $createdUser = $createUserService($userData);
    
        //Check data user created
        $this->assertEquals($userData->email, $createdUser->getEmail());
        $this->assertEquals($userData->name, $createdUser->getName());
        $this->assertEquals($userData->fullName, $createdUser->getFullName());

        //Check user persisted in db
        $persistedUser = $this->getUserById($createdUser->getId());
        $this->assertEquals($userData->email, $persistedUser->getEmail());
        $this->assertEquals($userData->name, $persistedUser->getName());
        $this->assertEquals($userData->fullName, $persistedUser->getFullName());

        //A user newly created is verified by default 
        $this->assertTrue($createdUser->isVerified());

        //A user newly created must has only a rol, ROLE_USER, and ROLE_VERIFIED_USER
        $listUserRoles = $createdUser->getRoles();
        sort($listUserRoles);
        $listExpectedRoles = ['ROLE_USER', 'ROLE_VERIFIED_USER'];
        sort($listExpectedRoles);
        $this->assertEquals($listExpectedRoles, $listUserRoles);
    }

    /**
     * Check we can't register a new user without email
     *
     * @return void
     */
    public function testRegisterUserWithoutEmail()
    {
        //Get an instance of registerUser instance
        $createUserService = $this->getCreateUserService();
        
        //Call service
        $userData = new CreateUserDTO(
            email: '',
            name: 'zulu',
            fullName: 'Zulu ambigu',
            password: '123456'
        );
        $this->expectException(\Exception::class);
        $createdUser = $createUserService($userData);
    }

    /**
     * We test that is not posible register a user without password
     *
     * @return void
     */
    public function testRegisterUserWithoutPassword()
    {
        //Get an instance of registerUser instance
        $createUserService = $this->getCreateUserService();
        
        //Call service
        $userData = new CreateUserDTO(
            email: 'pepe@zulu.com',
            name: 'pepe',
            fullName: 'Zulu ambigu',
            password: ''
        );
        $this->expectException(\Exception::class);
        $createdUser = $createUserService($userData);
    }

    /**
     * We test that is not posible register a new user without name
     *
     * @return void
     */
    public function testRegisterUserWithoutName()
    {
        //Get an instance of registerUser instance
        $registerUserService = $this->getCreateUserService();
        
        //Call service
        $userData = new CreateUserDTO(
            email: 'pepe@zulu.com',
            name: '',
            fullName: 'Zulu ambigu',
            password: '123456'
        );
        $this->expectException(\Exception::class);
        $createdUser = $registerUserService($userData);
    }

    /**
     * We test that is not posible register a new user without full name
     *
     * @return void
     */
    public function testRegisterUserWithoutFullName()
    {
        //Get an instance of registerUser instance
        $createUserService = $this->getCreateUserService();
        
        //Call service
        $userData = new CreateUserDTO(
            email: 'pepe@zulu.com',
            name: 'pepe',
            fullName: '',
            password: '123456'
        );
        $this->expectException(\Exception::class);
        $createdUser = $createUserService($userData);
    }

    /**
     * We check than 2 users can`t have same email
     *
     * @return void
     */
    public function testCantRegisterUsersWithSameEmail()
    {
        //Get an instance of registerUser instance
        $createUserService = $this->getCreateUserService();
        
        //We register user1
        $user1Data = new CreateUserDTO(
            email: 'sameEmail@gmail.com',
            name: 'user1',
            fullName: 'User1 full name',
            password: '123456'
        );
        $createdUser1 = $createUserService($user1Data);

        //We register user2 with same email than user1
        $user2Data = new CreateUserDTO(
            email: 'sameEmail@gmail.com',
            name: 'user2',
            fullName: 'User2 full name',
            password: '123456'
        );
        $this->expectException(\Exception::class);
        $createdUser2 = $createUserService($user2Data);
    }

    /**
     * Get an user by their id
     *
     * @param integer $id
     * @return User
     */
    private function getUserById(int $id):User
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
    private function getCreateUserService():CreateUser
    {
        $container = static::getContainer();

        //We get dependencies
        $entityManager = $container->get('Doctrine\ORM\EntityManagerInterface');
        $userPasswordHasher = $container->get('Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface');

        $createUserService = new CreateUser(
            $entityManager,
            $userPasswordHasher
        );

        return $createUserService;   
    }
}
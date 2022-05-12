<?php
namespace App\Tests\Integration\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\User\RegisterUser;
use App\Service\User\RegisterUserDTO;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Test the service Use Case RegisterUser
 */
class RegisterUserTest extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();
    }

    /**
     * Test register a new user
     * Normal case
     * 
     * @return void
     */
    public function testRegisterUser()
    {
        //Get an instance of registerUser instance
        $emailVerifierMock = $this->createEmailVerifierMock();
        //We check that send email method was called once in each test
        $emailVerifierMock->expects($this->once())
            ->method('sendEmailConfirmation');
        $registerUserService = $this->getRegisterUserService($emailVerifierMock);

        //Call service
        $userData = new RegisterUserDTO(
            email : 'zulu@miemail.com',
            name : 'zulu',
            fullName : 'Zulu ambigu',
            password : '123456'
        );
        $createdUser = $registerUserService($userData);
    
        //Check data user created
        $this->assertEquals($userData->email, $createdUser->getEmail());
        $this->assertEquals($userData->name, $createdUser->getName());
        $this->assertEquals($userData->fullName, $createdUser->getFullName());

        //Check user persisted in db
        $persistedUser = $this->getUserById($createdUser->getId());
        $this->assertEquals($userData->email, $persistedUser->getEmail());
        $this->assertEquals($userData->name, $persistedUser->getName());
        $this->assertEquals($userData->fullName, $persistedUser->getFullName());

        //A user newly created is not verified
        $this->assertFalse($createdUser->isVerified());

        //A user newly created must has only a rol, ROLE_USER
        $this->assertEquals(['ROLE_USER'], $createdUser->getRoles());
    }

    /**
     * Check we can't register a new user without email
     *
     * @return void
     */
    public function testRegisterUserWithoutEmail()
    {
        //Get an instance of registerUser instance
        $registerUserService = $this->getRegisterUserService();
        
        //Call service
        $userData = new RegisterUserDTO(
            email : '',
            name : 'zulu',
            fullName : 'Zulu ambigu',
            password : '123456'
        );
        $this->expectException(\Exception::class);
        $createdUser = $registerUserService($userData);
    }

    /**
     * We test that is not posible register a user without password
     *
     * @return void
     */
    public function testRegisterUserWithoutPassword()
    {
        //Get an instance of registerUser instance
        $registerUserService = $this->getRegisterUserService();
        
        //Call service
        $userData = new RegisterUserDTO(
            email : 'pepe@zulu.com',
            name : 'pepe',
            fullName : 'Zulu ambigu',
            password : ''
        );
        $this->expectException(\Exception::class);
        $createdUser = $registerUserService($userData);
    }

    /**
     * We test that is not posible register a new user without name
     *
     * @return void
     */
    public function testRegisterUserWithoutName()
    {
        //Get an instance of registerUser instance
        $registerUserService = $this->getRegisterUserService();
        
        //Call service
        $userData = new RegisterUserDTO(
            email : 'pepe@zulu.com',
            name : '',
            fullName : 'Zulu ambigu',
            password : '123456'
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
        $registerUserService = $this->getRegisterUserService();
        
        //Call service
        $userData = new RegisterUserDTO(
            email : 'pepe@zulu.com',
            name : 'pepe',
            fullName : '',
            password : '123456'
        );
        $this->expectException(\Exception::class);
        $createdUser = $registerUserService($userData);
    }

    /**
     * We check than 2 users can`t have same email
     *
     * @return void
     */
    public function testCantRegisterUsersWithSameEmail()
    {
        //Get an instance of registerUser instance
        $registerUserService = $this->getRegisterUserService();
        
        //We register user1
        $user1Data = new RegisterUserDTO(
            email : 'sameEmail@gmail.com',
            name : 'user1',
            fullName : 'User1 full name',
            password : '123456'
        );
        $createdUser1 = $registerUserService($user1Data);

        //We register user2 with same email than user1
        $user2Data = new RegisterUserDTO(
            email : 'sameEmail@gmail.com',
            name : 'user2',
            fullName : 'User2 full name',
            password : '123456'
        );
        $this->expectException(\Exception::class);
        $createdUser2 = $registerUserService($user2Data);
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
     * Get an instance of RegisterUser service
     * We mock EmailVerifier dependency
     *
     * @return RegisterUser
     */
    private function getRegisterUserService(?object $emailVerifierMock=null):RegisterUser
    {
        //We get dependencies of the service
        $container = static::getContainer();
        
        $entityManager = $container->get('Doctrine\ORM\EntityManagerInterface');
        $userPasswordHasher = $container->get('Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface');
        //In case of email service, we create a mock
        $emailVerifier = $emailVerifierMock ?? $this->createEmailVerifierMock();
        
        $registerUserService = new RegisterUser(
            $entityManager,
            $userPasswordHasher,
            $emailVerifier
        );

        return $registerUserService;
    }

    /**
     * Create an EmailVerifier mock
     *
     * @return object
     */
    private function createEmailVerifierMock():object
    {
        $emailVerifierMock = $this->getMockBuilder(EmailVerifier::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['sendEmailConfirmation'])
            ->getMock();

        return $emailVerifierMock;
    }
}
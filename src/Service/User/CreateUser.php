<?php
namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUser
{
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;         
    }

    public function __invoke(CreateUserDTO $userData):User
    {
        //Validate user data
        if ($this->isEmptyString($userData->email)) {
            throw new \Exception('You must provide an email');
        }

        if ($this->isEmptyString($userData->password)) {
            throw new \Exception('You must provide a password');
        }
        if ($this->isEmptyString($userData->name)) {
            throw new \Exception('You must provide a name');
        }
        if ($this->isEmptyString($userData->fullName)) {
            throw new \Exception('You must provide a fulll name');
        }

        $user = new User();
        $user->setEmail($userData->email);
        $user->setName($userData->name);
        $user->setFullName($userData->fullName);
        
        //Users created (not self registered) are considered as verified
        $user->setIsVerified(true);
        
        //Set hashed password
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $userData->password
            )
        );     
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();    
        
        return $user;
    }

    /**
     * Check if a string is empty
     *
     * @param string $value
     * @return boolean
     */
    private function isEmptyString(string $value):bool
    {
        return trim($value) === '';
    }
}
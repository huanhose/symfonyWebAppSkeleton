<?php

namespace App\Service\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Entity\User;
use App\Service\User\RegisterUserDTO;
use App\Security\EmailVerifier;

/**
 * Service user case fro register a new user in App
 */
class RegisterUser
{
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, EmailVerifier $emailVerifier)
    {
       $this->entityManager = $entityManager;
       $this->userPasswordHasher = $userPasswordHasher;
       $this->emailVerifier = $emailVerifier;
    }

    public function __invoke(RegisterUserDTO $userData):User
    {
        //Validate user data
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
        
        //Set hashed password
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $userData->password
            )
        );     
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();    
        
        $this->sendEmailVerifierToUser($user);

        return $user;
    }

    /**
     * Send a Email to a User with a url to a page to verify this newly created user
     *
     * @param User $user
     * @return void
     */
    private function sendEmailVerifierToUser(User $user)
    {
        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('info@myApp.com', 'AdminEmail'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
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

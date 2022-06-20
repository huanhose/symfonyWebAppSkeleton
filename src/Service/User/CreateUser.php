<?php

namespace App\Service\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Entity\User;
use App\Event\AfterCreateUserEvent;
use App\Service\Shared\DataValidator;

class CreateUser
{
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateUserDTO $userData): User
    {
        $dataValidator = new DataValidator();
        if ($dataValidator->isBlank($userData->password)) {
            throw new \Exception('You must provide a password');
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

        $this->dispatchAfertCreateEvent($user);

        return $user;
    }

    private function dispatchAfertCreateEvent(User $user)
    {
        $event = new AfterCreateUserEvent($user);
        $this->eventDispatcher->dispatch($event, 'user.after_create');
    }
}

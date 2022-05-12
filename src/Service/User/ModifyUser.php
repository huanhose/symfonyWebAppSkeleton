<?php
namespace App\Service\User;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\ModifyUserDTO;
use Exception;

/**
 * Service Use case to modify user data
 * Allow modify single, subset of fields
 * Not change password. Must use specialized service
 */
class ModifyUser
{
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function __invoke(ModifyUserDTO $userData):User
    {        
        $user = $this->userRepository->find($userData->id);

        //Only set fields that are not null in DTO
        if (null !== $userData->email) {
            $user->setEmail($userData->email);
        }

        if (null !== $userData->name) {
            $user->setName($userData->name);
        }

        if (null !== $userData->fullName) {
            $user->setFullName($userData->fullName);
        }

        if (null !== $userData->listAppRoles) {
            $user->setAppRoles($userData->listAppRoles);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();    
        
        return $user;
    }
}
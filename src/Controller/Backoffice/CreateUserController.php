<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\User\CreateUserDTO;
use App\Service\User\CreateUser;
use App\Entity\User;
use App\Form\CreateUserFormType;

class CreateUserController extends AbstractController
{
    private CreateUser $createUserService;

    public function __construct(CreateUser $createUserService)
    {
        $this->createUserService = $createUserService;
    }

    #[Route('/backoffice/createUser', name: 'app_createUser')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($form->get('plainPassword')->getData());
            $userCreated = $this->createUser($user);

            //Redirect to user profile page to complete or modify other fields
            $this->addFlash('userProfileForm_notice', 'User created successfully');
            return $this->redirectToRoute('users_user_profile', [
                'id' => $userCreated->getId()
            ]);
        }

        return $this->render('users/create_user.html.twig', [
            'createUserForm' => $form->createView(),
        ]);
    }

    private function createUser(User $user): User
    {
        $userCreated = $this->createUserService->__invoke(
            new CreateUserDTO(
                $email = $user->getEmail(),
                $name = $user->getName(),
                $fullName = $user->getFullName(),
                $password = $user->getPassword(),
            )
        );

        return $userCreated;
    }
}

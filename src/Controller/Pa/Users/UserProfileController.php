<?php

namespace App\Controller\Pa\Users;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use App\Entity\User;
use App\Form\UserFormType;
use App\Service\User\ModifyUser;
use App\Service\User\ModifyUserDTO;

/**
 * Form to edit user profile
 * - An admin can access all user profiles
 * - Normal users can only access their own profile
 */
class UserProfileController extends AbstractController
{
    private ModifyUser $modifyUserService;

    public function __construct(ModifyUser $modifyUserService)
    {
        $this->modifyUserService = $modifyUserService;
    }

    #[Route('/pa/users/user/{id}', name: 'users_user_profile', requirements: ['id' => '\d+'])]
    public function index(User $user, Request $request): Response
    {
        $this->checkUserAccess($user);

        $form = $this->loadDataFromUserToForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFormDataIntoUser($form, $user);

            //Message saved changes to form
            $this->addFlash(
                'userProfileForm_notice',
                'User Profile changed sucesfully'
            );

            //Redirect to user profile page to have clean urls
            return $this->redirectToRoute('users_user_profile', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('users/user_profile/user_profile.html.twig', [
            'controller_name' => 'UserProfileController',
            'userForm' => $form->createView(),
            'canAssignRoles' => $this->canAssignRoles()
        ]);
    }

    /**
     * Load data from user for form class, to be rendered in the view
     * Return a new instance of FormInterface
     *
     * @param User $user
     * @return FormInterface
     */
    private function loadDataFromUserToForm(User $user): FormInterface
    {
        $form = $this->createForm(UserFormType::class, $user, [
            'canAssignRoles' => $this->canAssignRoles()
        ]);

        //After loaded common data, we load suported roles (that can be selected in profile) in form
        $form->get('listRoles')->setData($user->getAppRoles());

        return $form;
    }

    /**
     * Get data from a form (form submit) and call modifyUser service with these data
     *
     * @param FormInterface $form
     * @param User $user
     * @return void
     */
    private function saveFormDataIntoUser(FormInterface $form, User $user)
    {
        $listRoles = null;
        if ($this->canAssignRoles()) {
            $listRoles = $form->get('listRoles')->getData();
        }

        $userData = new ModifyUserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName(),
            fullName: $user->getFullName(),
            listAppRoles: $listRoles
        );
        $this->modifyUserService->__invoke($userData);
    }

    /**
     * Check if a user can access an user profile
     * If not, throw a AccessDeniedException
     *
     * @param User $user
     * @throws AccessDeniedException
     * @return void
     */
    private function checkUserAccess(User $user)
    {
        //Admins can access all user profiles
        if ($this->isAdmin()) {
            return;
        }

        //If not admin, only current user can access thier own profile
        $connectedUserId = $this->getUser()?->getId();
        if ($connectedUserId !== $user->getId()) {
            throw new AccessDeniedException('Access denied to user profile');
        }
    }

    /**
     * Check if connected user is an admin
     *
     * @return boolean
     */
    private function isAdmin(): bool
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    /**
     * Check if connected user can assign roles to user profile
     * Only an admin can
     *
     * @return boolean
     */
    private function canAssignRoles(): bool
    {
        return $this->isGranted('ROLE_ADMIN');
    }
}

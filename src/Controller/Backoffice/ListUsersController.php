<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

/**
 * Page that show a list of registered users. Allow actions like create, edit or delete users
 */
class ListUsersController extends AbstractController
{
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/backoffice/listUsers', name: 'backoffice_list_users')]
    public function index(): Response
    {
        $listUsers = $this->getListUsers();

        return $this->render('backoffice/list_users.html.twig', [
            'controller_name' => 'ListUsersController',
            'list_users' => $listUsers
        ]);
    }

    /**
     * Get All registered users
     *
     * @return iterable
     */
    private function getListUsers(): iterable
    {
        $listUsers = $this->userRepository->findAll();
        return $listUsers;
    }
}

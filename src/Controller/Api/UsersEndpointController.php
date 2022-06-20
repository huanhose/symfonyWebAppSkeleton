<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * API to manage Users.
 * CRUD operations
 */
#[Route('/api/users/')]
class UsersEndpointController extends ApiController
{
    /**
     * GET users/{id}
     * Get user data identified by id
     * Returns User object
     */
    #[Route('{id}', name: 'users_getUser', requirements: ['id' => '\d+'], methods: 'GET')]
    public function getUser(int $id, UserRepository $userRepository, SerializerInterface $serializer)
    {
        $user = $userRepository->find($id);
        if (null ===  $user) {
            throw new NotFoundHttpException("User $id not found");
        }

        $jsonData = $serializer->serialize($user, 'json');
        return JsonResponse::fromJsonString($jsonData);
    }

    /**
     * GET users/
     * Get all users registered in the system
     * Return array of user objects
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @return void
     */
    #[Route('', name: 'users_getAllUser', methods: 'GET')]
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $listUsers = $userRepository->findAll();

        $jsonData = $serializer->serialize($listUsers, 'json');
        $statusCode = (count($listUsers) == 0) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        return JsonResponse::fromJsonString($jsonData, $statusCode);
    }
}

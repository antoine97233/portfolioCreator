<?php

namespace App\Controller\API;

use App\DTO\PaginationDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Requirement\Requirement;

class UsersController extends AbstractController
{

    #[Route("/api/users", name: "api_users_get", methods: ["GET"])]
    public function index(
        UserRepository $userRepository,
        #[MapQueryString()]
        ?PaginationDTO $paginationDTO = null
    ) {
        $users = $userRepository->paginateUsers($paginationDTO?->page);
        return $this->json($users, 200, [], [
            'groups' => ['users.index']
        ]);
    }


    #[Route("/api/users/{id}", requirements: ['id' => Requirement::DIGITS])]
    public function show(User $user)
    {

        return $this->json($user, 200, [], [
            'groups' => ['users.index', 'users.show']
        ]);
    }
}

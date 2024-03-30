<?php

namespace App\Controller\API;

use App\DTO\PaginationDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UsersController extends AbstractController
{

    #[Route("/api/users", name: "api_users_get", methods: ["GET"])]
    public function index(
        UserRepository $userRepository,
        // #[MapQueryString()]
        // ?PaginationDTO $paginationDTO = null
    ) {
        $users = $userRepository->findAll();
        return $this->json($users, 200, [], [
            'groups' => ['users.index']
        ]);
    }


    #[Route("/api/users/{id}", requirements: ['id' => Requirement::DIGITS])]
    public function show(
        User $user,
        EntityManagerInterface $em,
        int $id

    ) {

        $user = $em->getRepository(User::class)->findUserforApi($id);


        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        return $this->json($user, 200, []);
    }


    #[Route("/api/me/")]
    #[IsGranted("ROLE_USER")]
    public function me()
    {
        return $this->json($this->getUser());
    }
}

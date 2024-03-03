<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

class UsersController extends AbstractController
{

    #[Route("/api/users")]
    public function index(UserRepository $userRepository, Request $request, SerializerInterface $serializer)
    {

        $users = $userRepository->paginateUsers($request->query->getInt('page', 1));

        // dd($serializer->serialize($users, 'csv', [
        //     'groups' => ['users.index']
        // ]));

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

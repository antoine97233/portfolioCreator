<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {

        $users = $userRepository->findVisible(true);

        return $this->render('home/index.html.twig', [
            'users' => $users,
        ]);
    }


    #[Route('/users', name: 'users', methods: ['GET'])]
    public function showUsers(UserRepository $userRepository, SkillRepository $skillRepository, Request $request): Response
    {


        $page = $request->query->getInt('page', 1);

        $skills = $skillRepository->findAllWithCount();

        $users = $userRepository->paginateUsers($page);

        $usersTotal = $userRepository->findAllWithCount();
        $maxPage = ceil($usersTotal / 4);

        return $this->render('home/userList.html.twig', [
            'users' => $users,
            'maxPage' => $maxPage,
            'page' => $page,
            'usersTotal' => $usersTotal,
            'skills' => $skills,
        ]);
    }





    #[Route('/{slug}-{id}', name: 'user', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'], methods: ['GET'])]
    public function showUser(string $slug, int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->findUserWithMedia($id);

        if ($user->getSlug() !== $slug) {
            return $this->redirectToRoute('user', ['slug' => $user->getSlug(), 'id' => $user->getId()]);
        }

        return $this->render('home/user.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user
        ]);
    }
}

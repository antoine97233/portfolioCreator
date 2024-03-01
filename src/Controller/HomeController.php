<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findVisible(true);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'users' => $users
        ]);
    }




    #[Route('/{slug}-{id}', name: 'user', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'], methods: ['GET'])]
    public function show(string $slug, int $id, EntityManagerInterface $em): Response
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

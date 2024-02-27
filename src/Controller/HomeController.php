<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $query = $request->query->get('filterAvailability');

        if ($query === 'on') {
            $users = $em->getRepository(User::class)->findOpenToWork(true);
        } else {
            $users = $em->getRepository(User::class)->findAll();
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'users' => $users
        ]);
    }




    #[Route('/{slug}-{id}', name: 'user', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, UserRepository $repository): Response
    {
        $user = $repository->find($id);

        if ($user->getSlug() !== $slug) {
            return $this->redirectToRoute('user', ['slug' => $user->getSlug(), 'id' => $user->getId()]);
        }

        return $this->render('home/user.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user
        ]);
    }
}

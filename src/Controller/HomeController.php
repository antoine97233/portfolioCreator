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
use Symfony\Component\Routing\Requirement\Requirement;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(
        UserRepository $userRepository,
        Request $request
    ): Response {

        $users = $userRepository->findVisible(true);

        return $this->render('public/index.html.twig', [
            'users' => $users,
            'query' => (string) $request->query->get('q', '')

        ]);
    }


    #[Route('/users', name: 'users', methods: ['GET', 'POST'])]
    public function showUsers(
        UserRepository $userRepository,
        SkillRepository $skillRepository,
        Request $request
    ): Response {
        $page = $request->query->getInt('page', 1);
        $skills = $skillRepository->findAllWithCount();

        $usersTotal = $userRepository->findAllWithCount();
        $selectedSkills = [];

        if ($request->isMethod('GET')) {
            if ($request->query->has('selectedSkills')) {
                $selectedSkills = $request->query->all()['selectedSkills'];
                $users = $userRepository->findBySkills($selectedSkills);
                $usersTotal = count($users);
            } elseif ($request->query->has('removeFilter')) {
                $selectedSkills = [];
                return $this->redirectToRoute('users');
            } else {
                $users = $userRepository->paginateUsers($page);
            }
        }

        return $this->render('public/userList.html.twig', [
            'users' => $users,
            'usersTotal' => $usersTotal,
            'skills' => $skills,
            'selectedSkills' => $selectedSkills,
        ]);
    }







    #[Route('/{slug}/{id}', name: 'user', requirements: ['id' => Requirement::DIGITS, 'slug' => '[a-z0-9-]+'], methods: ['GET'])]
    public function showUser(
        string $slug,
        int $id,
        EntityManagerInterface $em
    ): Response {
        $user = $em->getRepository(User::class)->findUserWithMedia($id);

        if ($user->getSlug() !== $slug) {
            return $this->redirectToRoute('user', ['slug' => $user->getSlug(), 'id' => $user->getId()]);
        }

        return $this->render('public/user.html.twig', [
            'user' => $user
        ]);
    }
}

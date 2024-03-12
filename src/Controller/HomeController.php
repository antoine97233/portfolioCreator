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

    /**
     * Affiche la page d'accueil avec le composant de recherche d'utilisateur
     *
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(
        UserRepository $userRepository,
        Request $request
    ): Response {

        $users = $userRepository->findUserVisible(true);

        return $this->render('public/index.html.twig', [
            'users' => $users,
            'query' => (string) $request->query->get('q', '')

        ]);
    }

    /**
     * Affiche la liste des utilisateurs avec les filtres
     *
     * @param UserRepository $userRepository
     * @param SkillRepository $skillRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/users', name: 'users', methods: ['GET', 'POST'])]
    public function showUsers(
        UserRepository $userRepository,
        SkillRepository $skillRepository,
        Request $request
    ): Response {
        $skills = $skillRepository->findSkillsWithCount();
        $selectedSkills = [];
        $isOpenToWork = $request->query->get('filterAvailability', false);

        if ($request->isMethod('GET')) {
            if ($request->query->has('selectedSkills')) {
                $selectedSkills = $request->query->all()['selectedSkills'];
                if ($isOpenToWork) {
                    $users = $userRepository->findUserBySkillsAndOpenToWork($selectedSkills, true, true);
                } else {
                    $users = $userRepository->findUserBySkills($selectedSkills, true);
                }
            } elseif ($request->query->has('removeFilter')) {
                return $this->redirectToRoute('users');
            } else {
                if ($isOpenToWork) {
                    $users = $userRepository->findUserByOpentoWorkandVisible(true, true);
                } else {
                    $users = $userRepository->findUserVisible(true);
                }
            }
        }

        $usersTotal = count($users);

        return $this->render('public/userList.html.twig', [
            'users' => $users,
            'usersTotal' => $usersTotal,
            'skills' => $skills,
            'selectedSkills' => $selectedSkills,
            'isOpenToWork' => $isOpenToWork,
        ]);
    }


    /**
     * Affiche le profil d'un utilisateur avec toutes ses informations
     *
     * @param string $slug de l'utilisateur
     * @param integer $id de l'utilisateur
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/{slug}/{id}', name: 'user', requirements: ['id' => Requirement::DIGITS, 'slug' => '[a-z0-9-]+'], methods: ['GET'])]
    public function showUser(
        string $slug,
        int $id,
        EntityManagerInterface $em
    ): Response {
        $user = $em->getRepository(User::class)->findUserWithAll($id);

        if ($user->getSlug() !== $slug) {
            return $this->redirectToRoute('user', ['slug' => $user->getSlug(), 'id' => $user->getId()]);
        }

        return $this->render('public/user.html.twig', [
            'user' => $user
        ]);
    }
}

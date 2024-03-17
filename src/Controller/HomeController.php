<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use App\Service\UserSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{

    private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

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

        $users = $userRepository->findUserVisible(true, true);

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
    #[Route('/users', name: 'users', methods: ['GET'])]
    public function showUsers(
        UserRepository $userRepository,
        SkillRepository $skillRepository,
        Request $request
    ): Response {
        $selectedSkills = [];
        $isVisible = true;

        if ($request->isMethod('GET')) {
            $isOpenToWork = $request->query->get('isUserAvalaible');
            $skills = $skillRepository->findSkillsWithCount($isOpenToWork);

            if ($request->query->has('selectedSkills')) {
                $selectedSkills = array_map('intval', $request->query->all()['selectedSkills']);
                $users = $userRepository->findUserBySkills($selectedSkills, $isVisible, $isOpenToWork);
            } elseif ($request->query->has('removeFilter')) {
                return $this->redirectToRoute('users');
            } else {
                $selectedSkills = [];
                $users = $userRepository->findUserBySkills($selectedSkills, $isVisible, $isOpenToWork);
            }
        }

        $users = $this->paginator->paginate(
            $users, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );


        $usersTotal = count($users);

        return $this->render('public/userList/index.html.twig', [
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

        return $this->render('public/user/userProfile.html.twig', [
            'user' => $user
        ]);
    }
}

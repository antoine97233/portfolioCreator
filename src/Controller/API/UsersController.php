<?php

namespace App\Controller\API;

use App\DTO\PaginationDTO;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\UserApiService;

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



    #[Route('/api/users/{id}', name: 'api_user', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function showUser(int $id, UserApiService $userApiService): JsonResponse
    {
        $baseURL = 'https://portfolio.antoine-jolivet.fr';
        $userData = $userApiService->getUserData($id, $baseURL);

        if (isset($userData['error'])) {
            return new JsonResponse($userData, JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($userData);
    }


    #[Route('/api/users/{id}/projects', name: 'api_user_projects', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function showProjectsByUser(int $id, UserApiService $userApiService): JsonResponse
    {
        $baseURL = 'https://portfolio.antoine-jolivet.fr';
        $userData = $userApiService->getUserProjectsData($id, $baseURL);

        if (isset($userData['error'])) {
            return new JsonResponse($userData, JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($userData);
    }

    #[Route('/api/users/{id}/projects/{projectId}', name: 'api_user_project', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function showProjectById(int $id, int $projectId, UserApiService $userApiService): JsonResponse
    {
        $baseURL = 'https://portfolio.antoine-jolivet.fr';
        $projectData = $userApiService->getProjectData($id, $projectId, $baseURL);

        if (isset($projectData['error'])) {
            return new JsonResponse($projectData, JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($projectData);
    }


    #[Route("/api/me/")]
    #[IsGranted("ROLE_USER")]
    public function me()
    {
        return $this->json($this->getUser());
    }
}

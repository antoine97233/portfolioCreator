<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserApiService
{
    private string $baseURL = '';
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUserData(int $userId, string $baseURL): array
    {
        $this->baseURL = $baseURL;
        $user = $this->entityManager->getRepository(User::class)->findUserWithSkills($userId);

        if (!$user) {
            return ['error' => 'User not found'];
        }

        $scoreSkills = [];
        foreach ($user->getScoreSkills() as $scoreSkill) {
            $skillTitle = $scoreSkill->getSkill()->getTitle();

            $scoreSkills[] = [
                'id' => $scoreSkill->getId(),
                'skillTitle' => $skillTitle,
            ];
        }

        $thumbnail = $user->getMedia() ? $this->getFullImagePath($user->getMedia()->getThumbnail()) : null;

        return [
            'id' => $user->getId(),
            'fullName' => $user->getFullName(),
            'title' => $user->getTitle(),
            'subtitle' => $user->getSubtitle(),
            'shortDescription' => $user->getShortDescription(),
            'longDescription' => $user->getLongDescription(),
            'email' => $user->getEmail(),
            'tel' => $user->getTel(),
            'linkedin' => $user->getLinkedin(),
            'github' => $user->getGithub(),
            'thumbnail' => $thumbnail,
            'scoreSkills' => $scoreSkills,
        ];
    }

    public function getUserProjectsData(int $userId, string $baseURL): array
    {
        $this->baseURL = $baseURL;
        $projects = $this->entityManager->getRepository(Project::class)->findProjectsByUser($userId);

        if (empty($projects)) {
            return ['error' => 'No projects found for this user'];
        }

        $projectData = [];
        foreach ($projects as $project) {
            $projectData[] = $this->getProjectData($userId, $project->getId(), $baseURL);
        }

        return $projectData;
    }


    public function getProjectData(int $userId, int $projectId, string $baseURL): array
    {
        $this->baseURL = $baseURL;

        $project = $this->entityManager->getRepository(Project::class)->findProjectWithSkills($projectId);

        if (!$project) {
            return ['error' => 'Project not found'];
        }

        $thumbnail = $project->getMedia() ? $this->getFullImagePath($project->getMedia()->getThumbnail()) : null;

        $skills = [];
        foreach ($project->getSkill() as $skill) {
            $skills[] = [
                'id' => $skill->getId(),
                'title' => $skill->getTitle(),
            ];
        }

        return [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
            'subtitle' => $project->getSubtitle(),
            'description' => $project->getDescription(),
            'longDescription' => $project->getLongDescription(),
            'link' => $project->getLink(),
            'github' => $project->getGithubLink(),
            'thumbnail' => $thumbnail,
            'skills' => $skills,
        ];
    }



    private function getFullImagePath(?string $imageName): ?string
    {
        if (!$imageName) {
            return null;
        }

        return $this->baseURL . '/images/users/' . $imageName;
    }
}

<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserApiService
{
    private string $baseURL;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUserData(int $userId, string $baseURL): array
    {
        $this->baseURL = $baseURL;
        $user = $this->entityManager->getRepository(User::class)->findUserforApi($userId);

        if (!$user) {
            return ['error' => 'User not found'];
        }

        $projects = [];
        foreach ($user->getProjects() as $project) {
            $thumbnail = $project->getMedia() ? $this->getFullImagePath($project->getMedia()->getThumbnail()) : null;

            $skills = [];
            foreach ($project->getSkill() as $skill) {
                $skills[] = [
                    'id' => $skill->getId(),
                    'title' => $skill->getTitle(),
                ];
            }

            $projects[] = [
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
            'username' => $user->getFullName(),
            'title' => $user->getTitle(),
            'subtitle' => $user->getSubtitle(),
            'shortDescription' => $user->getShortDescription(),
            'longDescription' => $user->getLongDescription(),
            'email' => $user->getEmail(),
            'tel' => $user->getTel(),
            'linkedin' => $user->getLinkedin(),
            'github' => $user->getGithub(),
            'thumbnail' => $thumbnail,
            'projects' => $projects,
            'scoreSkills' => $scoreSkills,
        ];
    }

    private function getFullImagePath(?string $imageName): ?string
    {
        if (!$imageName) {
            return null;
        }

        return $this->baseURL . '/images/' . $imageName;
    }
}

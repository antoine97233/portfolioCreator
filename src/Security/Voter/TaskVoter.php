<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    public const DELETE = 'delete';
    public const EDIT = 'edit';
    public const SHOW = 'show';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Task && \in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($subject, $user);

            default:
                return false;
        }
    }

    private function canDelete(Task $task, User $user): bool
    {
        $isTaskProject = $task->getProject() !== null;
        $isTaskExperience = $task->getExperience() !== null;

        if ($isTaskProject && $task->getProject()->getUser()->getId() === $user->getId()) {
            return true;
        }

        if ($isTaskExperience && $task->getExperience()->getUser()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }
}

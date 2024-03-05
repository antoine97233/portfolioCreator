<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const ADD = 'TASK_ADD';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (in_array($attribute, [self::ADD])) || (in_array($attribute, [self::EDIT]))
            && $subject instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::ADD:
                return true;
            case self::EDIT:
                if ($subject->getExperience() && $subject->getExperience()->getUser()->getId() === $user->getId()) {
                    return true;
                }

                if ($subject->getProject() && $subject->getProject()->getUser()->getId() === $user->getId()) {
                    return true;
                }

                return false;
        }

        return false;
    }
}

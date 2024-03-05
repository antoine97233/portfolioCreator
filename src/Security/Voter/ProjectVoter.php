<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
    public const EDIT = 'PROJECT_EDIT';
    public const VIEW = 'PROJECT_VIEW';
    public const ADD = 'PROJECT_ADD';
    public const LIST = 'PROJECT_LIST';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (in_array($attribute, [self::ADD, self::LIST])) || (in_array($attribute, [self::EDIT, self::VIEW]))
            && $subject instanceof \App\Entity\Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $subject->getUser()->getId() === $user->getId();
                break;

            case self::VIEW:
            case self::LIST:
            case self::ADD:
                return true;
                break;
        }

        return false;
    }
}

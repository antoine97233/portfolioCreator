<?php

namespace App\Security\Voter;

use App\Entity\Experience;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ExperienceVoter extends Voter
{


    public const EDIT = 'EXPERIENCE_EDIT';
    public const VIEW = 'EXPERIENCE_VIEW';
    public const ADD = 'EXPERIENCE_ADD';
    public const LIST = 'EXPERIENCE_LIST';
    public const LIST_ALL = 'EXPERIENCE_ALL';
    public const DELETE = 'EXPERIENCE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return (in_array($attribute, [self::ADD, self::LIST])) || (in_array($attribute, [self::EDIT, self::VIEW]))
            && $subject instanceof \App\Entity\Experience;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $subject->getUser()->getId() === $user->getId();
                break;
            case self::DELETE:
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

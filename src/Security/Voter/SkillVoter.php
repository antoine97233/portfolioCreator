<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SkillVoter extends Voter
{
    public const EDIT = 'SKILL_EDIT';
    public const LIST = 'SKILL_LIST';
    public const CREATE = 'SKILL_CREATE';
    public const VIEW = 'SKILL_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return (in_array($attribute, [self::LIST])) || (in_array($attribute, [self::EDIT, self::VIEW]))
            && $subject instanceof \App\Entity\Skill;
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
            case self::CREATE:
                return $subject->getUser()->getId() === $user->getId();
                break;
            case self::LIST:
            case self::VIEW:
                return true;
                break;
        }

        return false;
    }
}

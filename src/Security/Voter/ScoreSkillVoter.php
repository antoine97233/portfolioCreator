<?php

namespace App\Security\Voter;

use App\Entity\ScoreSkill;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ScoreSkillVoter extends Voter
{
    public const DELETE = 'delete';
    public const EDIT = 'edit';
    public const SHOW = 'show';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return $subject instanceof ScoreSkill && \in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }

    /**
     * voteOnAttribute
     *
     * @param  mixed $attribute
     * @param  ScoreSkill|null $subject
     * @param  mixed $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                return $subject->getUser()->getId() === $user->getId();
                break;
            case self::SHOW:
                return true;
                break;
        }

        return false;
    }
}

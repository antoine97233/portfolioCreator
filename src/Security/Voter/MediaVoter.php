<?php

namespace App\Security\Voter;

use App\Entity\Media;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MediaVoter extends Voter
{
    public const DELETE = 'delete';
    public const EDIT = 'edit';
    public const SHOW = 'show';
    public const ADD = 'add';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Media && \in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
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

    private function canDelete(Media $media, User $user): bool
    {
        $isUserMedia = $media->getUser() !== null;
        $isProjectMedia = $media->getProject() !== null;

        if ($isUserMedia && $media->getUser()->getId() === $user->getId()) {
            return true;
        }

        if ($isProjectMedia && $media->getProject()->getUser()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }
}

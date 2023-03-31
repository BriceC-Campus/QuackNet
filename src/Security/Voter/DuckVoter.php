<?php

namespace App\Security\Voter;

use App\Entity\Duck;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class DuckVoter extends Voter
{
    public const EDIT = 'edit';
    public const VIEW = 'view';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Duck;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::VIEW:
                return $this->canView($subject, $user);
        }

        return false;
    }
    private function canView(Duck $duck, UserInterface $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($duck, $user)) {
            return true;
        }
        return false;
    }

    private function canEdit(Duck $duck, UserInterface $user): bool
    {
        return $user === $duck;
    }
}

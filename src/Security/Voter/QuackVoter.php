<?php

namespace App\Security\Voter;

use App\Entity\Duck;
use App\Entity\Quack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\String\u;

class QuackVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Quack;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $attribute = u($attribute)->upper();
        $user = $token->getUser();
        dd($user);
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

    private function canView(Quack $quack, Duck $duck): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($quack, $duck)) {
            return true;
        }
        return false;
    }

    private function canEdit(Quack $quack, Duck $duck): bool
    {
        return $duck === $quack->getDuck();
    }
}

<?php

namespace App\Security\Voter;

use App\Entity\Restaurant;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class RestaurantVoter extends Voter
{
    public const EDIT = 'RESTAURANT_EDIT';
    public const DELETE = 'RESTAURANT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE], true)
            && $subject instanceof Restaurant;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            $vote?->addReason('Vous devez être connecté.');

            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        if ($subject->getProprietaire() === $user) {
            return true;
        }

        $vote?->addReason('Seul le propriétaire du restaurant ou un administrateur peut effectuer cette action.');

        return false;
    }
}

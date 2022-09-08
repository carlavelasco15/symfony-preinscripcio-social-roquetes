<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Participant;
use App\Repository\UserEntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ParticipantVoter extends Voter
{
    private $security, $operaciones, $requestStack, $entity_id, $userEntityRepository;

    public function __construct(Security $security,
                                RequestStack $requestStack,
                                UserEntityRepository $userEntityRepository)
    {
        $this->security = $security;
        $this->operaciones = ['create', 'edit', 'delete', 'see'];
        $this->requestStack = $requestStack;
        $this->userEntityRepository = $userEntityRepository;
    }


    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, $this->operaciones)) {
            return false;
        }

        if (!$subject instanceof Participant) {
            return false;
        }

        return true;
    }

    protected function VoteOnAttribute(string $attribute, $participant, 
                                        TokenInterface $token): bool
    {
        $user = $token->getUser();

        if($user_entity_id = $this->requestStack->getSession()->get('user_entity_id')) {
            $this->entity_id = $this->userEntityRepository->find($user_entity_id)->getEntity()->getId();
        }

        if (!$user instanceof User) {
            return false;
        }

        $method = 'can' . ucfirst($attribute);

        return $this->$method($participant, $user);
    }


    private function canCreate(Participant $participant, User $user): bool
    {
        if ( ($user->isVerified()) && $this->security->isGranted('ROLE_PRESCRIPTOR') ) {
            return true;
        }
        return false;
    }


    private function canEdit(Participant $participant, User $user): bool
    {
        if ( ($user->isVerified()) && $this->security->isGranted('ROLE_ADMIN') ) {
            return true;
        }

        if ( $user->isVerified() &&
            $this->entity_id === $participant->getEntity()->getId() &&
            $this->security->isGranted('ROLE_PRESCRIPTOR')) 
        {
            return true;
        }

        return false;
    }


    private function canDelete(Participant $participant, User $user): bool
    {
        return $this->canEdit($participant, $user);
    }

    
    private function canSee(Participant $participant, User $user): bool
    {
        if ( ($user->isVerified()) && $this->security->isGranted('ROLE_PRESCRIPTOR') ) {
            return true;
        }
        return false;
    }
}

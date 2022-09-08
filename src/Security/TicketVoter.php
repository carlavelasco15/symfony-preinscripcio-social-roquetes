<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Ticket;
use App\Repository\UserEntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TicketVoter extends Voter
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

        if (!$subject instanceof Ticket) {
            return false;
        }

        return true;
    }

    protected function VoteOnAttribute(string $attribute, $ticket, 
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

        return $this->$method($ticket, $user);
    }


    private function canCreate(Ticket $ticket, User $user): bool
    {
        if ( ($user->isVerified()) && $this->security->isGranted('ROLE_EDITOR') ) {
            return true;
        }
        return false;
    }


    private function canEdit(Ticket $ticket, User $user): bool
    {
        if ( ($user->isVerified()) && $this->security->isGranted('ROLE_ADMIN') ) {
            return true;
        }

        if ( $user->isVerified() &&
            $this->entity_id === $ticket->getEntity()->getId() &&
            $this->security->isGranted('ROLE_EDITOR')) 
        {
            return true;
        }

        return false;
    }


    private function canDelete(Ticket $ticket, User $user): bool
    {
        return $this->canEdit($ticket, $user);
    }

    
    private function canSee(Ticket $ticket, User $user): bool
    {
        if ( ($user->isVerified()) && $this->security->isGranted('ROLE_PRESCRIPTOR') ) {
            return true;
        }

        if ( $user->isVerified() &&
            $this->entity_id === $ticket->getUser()->getEntity()->getId() &&
            $this->security->isGranted('ROLE_EDITOR')) 
        {
            return true;
        }
        
        return false;
    }

}

<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Activity;
use App\Repository\UserEntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ActivityVoter extends Voter
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

        if (!$subject instanceof Activity) {
            return false;
        }

        return true;
    }

    protected function VoteOnAttribute(string $attribute, $activity, 
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

        return $this->$method($activity, $user);
    }


    private function canCreate(Activity $activity, User $user): bool
    {
        if ( ($user->isVerified()) && $this->security->isGranted('ROLE_EDITOR') ) {
            return true;
        }
        return false;
    }


    private function canEdit(Activity $activity, User $user): bool
    {
        if ( ($user->isVerified()) && $this->security->isGranted('ROLE_ADMIN') ) {
            return true;
        }

        if ( $user->isVerified() &&
            $this->entity_id === $activity->getEntity()->getId() &&
            $this->security->isGranted('ROLE_EDITOR')) 
        {
            return true;
        }

        return false;
    }


    private function canDelete(Activity $activity, User $user): bool
    {
        return $this->canEdit($activity, $user);
    }

    
    private function canSee(Activity $activity, User $user): bool
    {
        if ( ($user->isVerified()) && $this->security->isGranted('ROLE_PRESCRIPTOR') ) {
            return true;
        }

        if ( $user->isVerified() &&
            $this->entity_id === $activity->getEntity()->getId() &&
            $this->security->isGranted('ROLE_EDITOR')) 
        {
            return true;
        }
        
        return false;
    }

}

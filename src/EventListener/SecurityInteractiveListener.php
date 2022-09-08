<?php
namespace App\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;

class SecurityInteractiveListener
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage, 
                                RequestStack $requestStack,
                                Security $security)
    {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
        $this->session = $requestStack;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $this->security->getUser();
        $userEntities = $user->getUserEntities();
       foreach ( $userEntities as $userEntity ) {
                $this->session->getSession()->set('user_entity_id', $userEntity->getEntity()->getId());
                $user->resetAndAddRole($userEntity->getRoles());
                break;
            }
       // We just need to set the new security token
       $token = new UsernamePasswordToken(
            $user,
            null,
           'main',
           $user->getRoles()
      );

      $this->session->getSession()->set('user', $token);

      // Update the current token to set the new role
      $this->tokenStorage->setToken($token);
    } 

}
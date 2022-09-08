<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\UserNotificationFormType;
use App\Repository\EntityRepository;
use App\Repository\UserRepository;
use App\Services\FileService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;


#[Route('/usuari', name: 'user_')]
class UserController extends AbstractController
{


    #[Route('/editar', name: 'edit')]
    public function edit(
        Request $request,
        UserRepository $userRepository,
        FileService $uploader
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $correo = $user->getEmail();
        $fichero = $user->getPicture();
        $formUserProfile = $this->createForm(UserFormType::class, $user);
        $formNotifications = $this->createForm(UserNotificationFormType::class, $user, [
            'action' => $this->generateUrl('user_edit_notification'),
        ]);
        $formUserProfile->handleRequest($request);
        if($formUserProfile->isSubmitted() && $formUserProfile->isValid()) {
            $file = $formUserProfile->get('picture')->getData();
            $email = $formUserProfile->get('email')->getData();
            $uploader->setTargetDirectory($this->getParameter('app.user_images.root'));
            if($file)
                $fichero = $uploader->replace($file, $fichero);
            $user->setPicture($fichero);
            if($email != $correo)
                $user->setIsVerified(0);
            $userRepository->add($user, true);
            $this->addFlash('success', "L'usuari s'ha modificat correctament.");
            return $this->redirectToRoute('user_list');
        }
        return $this->renderForm('user/home.html.twig', [
            'formUserProfile' => $formUserProfile,
            'formNotifications' => $formNotifications
        ]);
    }


    #[Route('/editar/notificacion', name: 'edit_notification')]
    public function editNotification(
        Request $request,
        UserRepository $userRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $formNotifications = $this->createForm(UserNotificationFormType::class,$user);
        $formNotifications->handleRequest($request);
        if($formNotifications->isSubmitted() && $formNotifications->isValid()) {
            $userRepository->add($user, true);
            $this->addFlash('success', "L'usuari s'ha modificat correctament.");
        }
        return $this->redirectToRoute('user_edit');
    }


    #[Route('/imatge/{picture}', name: 'picture')]
    public function showPicture(string $picture): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $ruta = $this->getParameter('app.user_images.root');
        $response = new BinaryFileResponse($ruta.'/'.$picture);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $picture,
            iconv('UTF-8', 'ASCII//TRANSLIT', $picture)
        );
        return $response;
    }


    #[Route('/imatge/eliminar/{id<\d+>}', name: 'picture_delete')]    
    public function deletePicture(
        FileService $uploader,
        EntityManagerInterface $em
    ): Response
    {
        /* $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); */
        $user = $this->getUser();
        if($file = $user->getPicture()) {
            $uploader->delete($file);
            $user->setPicture(NULL);
            $em->flush();
            $mensaje = 'Imatge de l\'usuari '. $user->getUserIdentifier().' eliminada.';
            $this->addFlash('success', $mensaje);
        }
        return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
    }


    #[Route('/entitats', name: 'entities')]
    public function chooseEntities(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $userEntities = $user->getUserEntities();
        return $this->render('user/entity.html.twig', [
            'userEntities' => $userEntities
        ]);
    }


    #[Route('/escollir/entitat/{id<\d+>}', name: 'choose_entity')]
    public function changeAccountAction(int $id,
                                        Request $request, 
                                        EntityManagerInterface $em,
                                        TokenStorageInterface $tokenStorage
                                        ) 
    {
        $user = $this->getUser();
        $userEntity = $em->getRepository('App\Entity\UserEntity')->getAccountModelByAccountIdAndUserId($id, $user->getId());
        $entity = $userEntity->getEntity();
        $user->resetAndAddRole($userEntity->getRoles());
        $token = new UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );
        $tokenStorage->setToken($token);
        $request->getSession()->set('user_entity_id', $userEntity->getId());
        if (in_array("ROLE_PRESCRIPTOR", $user->getRoles()))
            return new RedirectResponse($this->generateUrl('activity_list_prescriptor'));
        else if (in_array("ROLE_EDITOR", $user->getRoles()))
            return new RedirectResponse($this->generateUrl('activity_list_editor'));
        return new RedirectResponse($this->generateUrl('user_entities'));
    }
}

<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\UserNotificationFormType;
use App\Repository\UserRepository;
use App\Services\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
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
        /* $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); */

        $user = $this->getUser();
        $correo = $user->getEmail();
        $fichero = $user->getPicture();

        $formUserProfile = $this->createForm(UserFormType::class, $user);
        $formNotifications = $this->createForm(UserNotificationFormType::class, $user);
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

    #[Route('/imatge/{picture}', name: 'picture')]
    public function showPicture(string $picture): Response
    {
        //denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
    
    //denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    $user = $this->getUser();

    if($file = $user->getPicture()) {
        $uploader->delete($file);

        $user->setPicture(NULL);
        $em->flush();

        $mensaje = 'Imatge de l\'usuari '. $user->getUsername().' eliminada.';
        $this->addFlash('success', $mensaje);
    }

    return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
}

}

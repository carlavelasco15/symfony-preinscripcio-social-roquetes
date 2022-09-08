<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\User;
use App\Entity\UserEntity;
use App\Form\RegistrationFormType;
use App\Repository\EntityRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }


    #[Route('/register/{id<\d+>}', name: 'register')]
    public function register(Entity $entity,
                            Request $request, 
                            UserPasswordHasherInterface $userPasswordHasher, 
                            EntityManagerInterface $entityManager): Response
    {
        $userEntity = new UserEntity();
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            /* $user->addUserEntity($userEntity);
            $entity->addUserEntity($userEntity); */

            $userEntity->setEntity($entity);
            $userEntity->setUser($user);

            $entityManager->persist($userEntity);
            /* $entityManager->persist($entity); */
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@prescripciosocialroquetes.cat', 'Registre d\'usuari'))
                    ->to($user->getEmail())
                    ->subject('Confirmació de correu')
                    ->htmlTemplate('email/register_verification.html.twig')
            );
            return $this->redirectToRoute('activity_list');
        }
        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');
        if (null === $id) 
            return $this->redirectToRoute('register');
        $user = $userRepository->find($id);
        if (null === $user) 
            return $this->redirectToRoute('register');
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('register');
        }
        $this->addFlash('success', 'Tu email ha sido verificado.');

        return $this->redirectToRoute('register');
    }


    #[Route('/verify/resend/email', name: 'resend_verification')]
    public function resendVerificationEmail(Request $request, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
         // generate a signed url and email it to the user
         $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@prescripciosocialroquetes.cat', 'Registre d\'usuari'))
                ->to($user->getEmail())
                ->subject('Confirmació de correu')
                ->htmlTemplate('email/register_verification.html.twig')
        );
        $mensaje = "Operació realitzada, revista el teu email y fés clic a l'enllaç per completar la operació de registre. El missatge d'advertencia desapareixerà un cop completat el procés.";
        $this->addFlash('success', $mensaje);
        return $this->redirectToRoute('user_edit', [
            'id' => $user->getId()
        ]);
    }
}

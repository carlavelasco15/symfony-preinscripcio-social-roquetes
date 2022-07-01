<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Participant;
use App\Form\ActivityFormType;
use App\Form\ParticipantFormType;
use App\Repository\ActivityRepository;
use App\Repository\EntityRepository;
use App\Services\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/activitat", name="activity")
 */ 
class ActivityController extends AbstractController
{
    /**
     * @Route("s ", name="_list")
     */     
    public function index(ActivityRepository $activityRepository): Response
    {

        $activities = $activityRepository->findAll();

        return $this->render('activity/list.html.twig', [
            'activities' => $activities
        ]);
    }

    /**
     * @Route("/crear ", name="_create")
     */     
    public function create(
        Request $request,
        FileService $uploader,
        ActivityRepository $activityRepository): Response
    {
        $activity = new Activity();

        $form = $this->createForm(ActivityFormType::class, $activity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('picture')->getData();

            if($file)
                $activity->setPicture($uploader->upload($file));

            $weekday = $form->get('weekday')->getData();
            $start_hour = $form->get('start_hour')->getData();
            $end_hour = $form->get('end_hour')->getData();

            $activity->setSchedule($weekday . ' de ' . $start_hour->format('H:i') . ' a ' . $end_hour->format('H:i'));

            $activity->setPlacesTaken(0);
            $activity->setIsVisible(1);

            $activityRepository->add($activity, true);
            $this->addFlash('success', "S'ha afegir l'activitat correctament.");
            return $this->redirectToRoute('activity_list');
        }

        return $this->render('activity/create.html.twig', [
            'formulario' => $form->createView()
        ]);
    }
    

    /**
     * @Route("/editar/{id}", name="_edit")
     */     
    public function edit(ActivityRepository $activityRepository): Response
    {

        $activities = $activityRepository->findAll();

        return $this->render('activity/list.html.twig', [
            'activities' => $activities
        ]);
    }




     /**
     * @Route("/detall/{id}", name="_show")
     */     
    public function show(
        Activity $activity,
        EntityRepository $entityRepository): Response
    {
        $participant = new Participant();
        $newParticipantForm = $this->createForm(ParticipantFormType::class, $participant);

        return $this->renderForm('activity/show.html.twig', [
            'activity' => $activity,
            'newParticipantForm' => $newParticipantForm
        ]);
    }


     /**
     * @Route("/activity/picture/{picture}", name="_picture")
     */     
    public function showPicture(string $picture): Response
    {
        
        //denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $ruta = $this->getParameter('app.activity_images.root');

        $response = new BinaryFileResponse($ruta.'/'.$picture);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $picture,
            iconv('UTF-8', 'ASCII//TRANSLIT', $picture)
        );

        return $response;
    }
}

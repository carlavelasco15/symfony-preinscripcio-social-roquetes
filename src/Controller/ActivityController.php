<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Participant;
use App\Form\ActivityFormType;
use App\Form\ParticipantFormType;
use App\Repository\ActivityRepository;
use App\Repository\EntityRepository;
use App\Services\FileService;
use App\Services\PaginatorService;
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
     * @Route("s/{pagina} ",
     * defaults= {"pagina": 1},
     * name="_list")
     */     
    public function index(int $pagina, 
                        PaginatorService $paginator): Response
    {
        $paginator->setEntityType('App\Entity\Activity');

        $activities = $paginator->findAllEntities($pagina);

        return $this->render('activity/list.html.twig', [
            'activities' => $activities,
            'paginator' => $paginator
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
                
            /* Tractament de l'string segons el nombre de dies de la setmana de l'activitat */
            if (count($weekday) > 2) {
                $last_element = array_pop($weekday);
                $first_elements = implode(', ', $weekday);
                $weekday_new_array = [$first_elements, $last_element];
                $weekday_imploded = implode(' i ', $weekday_new_array); 
            } else {
                $weekday_imploded = implode(' i ', $weekday);
            }

            $start_hour = $form->get('start_hour')->getData();
            $end_hour = $form->get('end_hour')->getData();

            $activity->setSchedule($weekday_imploded . ' de ' . $start_hour->format('H:i') . ' a ' . $end_hour->format('H:i'));

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

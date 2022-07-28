<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Search;
use App\Entity\Participant;
use App\Entity\GetTicketStatus;
use App\Entity\Ticket;
use App\Form\ActivityAddParticipantFormType;
use App\Form\ActivityFormType;
use App\Form\ParticipantFormType;
use App\Form\SearchFormType;
use App\Repository\ActivityRepository;
use App\Repository\TicketRepository;
use App\Repository\TicketStatusRepository;
use App\Services\SimpleSearchService;
use App\Services\FileService;
use App\Services\PaginatorService;
use App\Services\TicketStatusService;
use Doctrine\ORM\EntityManagerInterface;
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
    public function list(int $pagina,
                        Request $request,
                        PaginatorService $paginator,
                        SimpleSearchService $searchService,
                        TicketStatusService $ticketStatus,
                        ActivityRepository $activityRepository,
                        TicketRepository $ticketRepository): Response
    {

        $busqueda = new Search();
        $busqueda->setEntity(Activity::class);

        $busqueda = $searchService->getSearchFromSession(Activity::class) ?? $busqueda;

        $searchForm = $this->createForm(SearchFormType::class, $busqueda, [
            'field_choices' => [
                'Nom' => 'name',
                'Rang d\'edat' => 'age_range_id',
                'Categoria' => 'category',
            ],
            'order_choices' => [
                'ID' => 'id',
                'Nom' => 'name',
                'Categoria' => 'category_id',
                'Entitat' => 'entity_id'
            ]
            ]);

            
        $searchForm->handleRequest($request);
        $searchService->setSearch($busqueda);


        $activities = $paginator->paginate(
            $activityRepository->searchActivityQuery($busqueda),
            $pagina
        );

        $searchService->storeSearchInSession($busqueda);


        if($searchForm->isSubmitted() && $searchForm->isValid())
            return $this->redirectToRoute('activity_list');


        return $this->renderForm('activity/list.html.twig', [
            'search' => $searchForm,
            'paginator' => $paginator,
            'activities' => $activities,
            'ticketRepository' => $ticketRepository
        ]);
    }




    /**
     * @Route("/crear", name="_create")
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
            $activity->setIsDeleted(0);
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
     * @Route("/editar/{id<\d+>}", name="_edit")
     */     
    public function edit(
            Activity $activity,
            Request $request,
            ActivityRepository $activityRepository,
            FileService $uploader
            ): Response
    {
        $fichero = $activity->getPicture();

        $form = $this->createForm(ActivityFormType::class, $activity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('picture')->getData();

            if($file)
                $fichero = $uploader->replace($file, $fichero);
            
            $activity->setPicture($fichero);

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



            $activityRepository->add($activity, true);
            $this->addFlash('success', 'Activitat actualitzada correctament.');

            return $this->redirectToRoute('activity_show', ['id' => $activity->getId()]);
        }


        return $this->renderForm('activity/edit.html.twig', [
            'formulario' => $form,
            'activity' => $activity,
        ]);
    }


     /**
     * @Route("/detall/{id<\d+>}", name="_show")
     */     
    public function show(
        Activity $activity,
        TicketRepository $ticketRepository
       ): Response
    {

        $participant = new Participant();
        $tickets = $ticketRepository->ticketsPerActivity($activity);
        $ticketsWaitingList = $ticketRepository->ticketsWaitingListPerActivity($activity);
        $newParticipantForm = $this->createForm(ParticipantFormType::class, $participant);

        $oldParticipantForm = $this->createForm(ActivityAddParticipantFormType::class, NULL, [
            'action' => $this->generateUrl('ticket_create_in_activity', ['id' => $activity->getId()])
        ]);


        return $this->renderForm('activity/show.html.twig', [
            'activity' => $activity,
            'tickets' => $tickets,
            'ticketsWaitingList' => $ticketsWaitingList,
            'newParticipantForm' => $newParticipantForm,
            'oldParticipantForm' => $oldParticipantForm
        ]);
    }

    /**
     * @Route("/search/forget", name="_forget_search")
     */     
    public function forgetSearch(
        SimpleSearchService $searchService
        ): Response
    {
        $searchService->removeSearchFromSession(Activity::class);
        $this->addFlash('success', 'Filtre eliminat.');
        return $this->redirectToRoute('activity_list');
    }



    /**
     * @Route("/visibilitat/{id<\d+>}", name="_toggle_visibility")
     */     
    public function toggleVisibility(
        Activity $activity,
        ActivityRepository $activityRepository,
        ): Response
    {
        $visibilityChange =  $activity->isIsVisible() ? 0 : 1;
        $activity->setIsVisible($visibilityChange);

        $activityRepository->add($activity, true);

        $mensaje = $visibilityChange ? "L'activitat " . $activity->getName() . " és visible pel servei." : "L'activitat '" . $activity->getName() . "' s'ha ocultat pel servei.";
        $this->addFlash('success', $mensaje);
       
        return $this->redirectToRoute('activity_list');
    }



     /**
     * @Route("/eliminar/{id<\d+>}", name="_delete")
     */     
    public function delete(
        Activity $activity,
        FileService $uploader,
        ActivityRepository $activityRepository
        ): Response
    {

        /* TODO: AFEGIR VOTERS SI ACTIVITAT ELIMNARA NO MOSTRAR SHOW/DELETE/EDIT DE L'ACTIVITAT */
        $file = $activity->getPicture();

        if($file)
            $uploader->delete($file);
        $activity->setPicture(NULL);


        $activity->setIsDeleted(1);
        $activityRepository->add($activity, true);

        $this->addFlash('success', 'Activitat eliminada correctament.');


        return $this->redirectToRoute('activity_list');
    }


     /**
     * @Route("/imatge/{picture}", name="_picture")
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


     /**
     * @Route("/imatge/eliminar/{id<\d+>}", 
     * name="_picture_delete")
     */     
    public function deletePicture(
            Activity $activity,
            FileService $uploader,
            EntityManagerInterface $em
        
        ): Response
    {
        
        //denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($file = $activity->getPicture()) {
            $uploader->delete($file);

            $activity->setPicture(NULL);
            $em->flush();

            $mensaje = 'Imatge de l\'activitat '. $activity->getPicture().' eliminada.';
            $this->addFlash('success', $mensaje);
        }

        return $this->redirectToRoute('activity_edit', ['id' => $activity->getId()]);
    }


     /**
     * @Route("/afegir/participant/{id<\d+>}", 
     * name="_add_participant")
     */     
    public function addParticipant(
            Activity $activity,
            Request $request,
            ActivityRepository $activityRepository,
            TicketStatusRepository $ticketStatusRepository,
            TicketRepository $ticketRepository
        
        ): Response
    {
        
        //denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $formularioAddParticipant = $this->createForm(ActivityAddParticipantFormType::class);
        $formularioAddParticipant->handleRequest($request);
        $participant = $formularioAddParticipant->getData()['participant'];

        $ticket = new Ticket();
        $ticketStatus = $ticketStatusRepository->find(GetTicketStatus::OPEN);

        /* TODO: FALTA FLASH */

        $ticket->setParticipant($participant)
                ->setActivity($activity)
                ->setTicketStatus($ticketStatus)
                ->setIsDeleted(0);

        $ticketRepository->add($ticket, true);
                
        $activity->setPlacesTaken($ticketRepository->countTicketsPerActivity($activity));
        $activityRepository->add($activity, true);

        return $this->redirectToRoute('activity_show', ['id' => $activity->getId()]);
    }


    /**
     * @Route("/eliminar/participant/{id<\d+>}", 
     * name="_remove_participant")
     */ 
    public function removeParticipant(
        Ticket $ticket,
        TicketStatusRepository $ticketStatusRepository,
        ActivityRepository $activityRepository,
        TicketRepository $ticketRepository
    ): Response {

        $activity = $ticket->getActivity();
        $ticketStatus = $ticketStatusRepository->find(GetTicketStatus::CLOSED_EQUIPMENT);

        /* TODO: FALTA FLASH */

        $ticket->setIsDeleted(1)
                ->setTicketStatus($ticketStatus);

        $ticketRepository->add($ticket, true);

        $activity->setPlacesTaken($ticketRepository->countTicketsPerActivity($activity));
        $activityRepository->add($activity, true);

        return $this->redirectToRoute('activity_show', ['id' => $activity->getId()]); 
    }

}

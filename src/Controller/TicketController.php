<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Participant;
use App\Entity\Search;
use App\Entity\GetTicketStatus;
use App\Entity\Ticket;
use App\Form\ActivityAddParticipantFormType;
use App\Form\SearchFormType;
use App\Repository\ActivityRepository;
use App\Repository\ParticipantRepository;
use App\Repository\TicketRepository;
use App\Repository\TicketStatusRepository;
use App\Services\SimpleSearchService;
use App\Services\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tiquet', name: 'ticket_')]
class TicketController extends AbstractController
{


    #[Route('s/{id<\d+>}/{pagina<\d+>}', defaults: ['pagina' => 1], name: 'list')]
    public function list(
            Participant $participant,
            int $pagina,
            Request $request,
            PaginatorService $paginator,
            SimpleSearchService $searchService,
            TicketRepository $ticketRepository
            ):Response 
    {

        $busqueda = new Search();
        $busqueda->setEntity(Ticket::class);
           
        $busqueda = $searchService->getSearchFromSession(Ticket::class) ?? $busqueda;
        $busqueda->setEntityId($participant);
    

        $searchForm = $this->createForm(SearchFormType::class, $busqueda, [
            'field_choices' => [
                'Nom participant' => Activity::class . '.name',
            ],
            'order_choices' => [
                'ID' => 'id',
                'Nom' => 'name',
            ]
        ]);

        $searchForm->handleRequest($request);
        $searchService->setSearch($busqueda);

        $tickets = $paginator->paginate(
            $ticketRepository->searchTicketsByParticipant($busqueda),
            $pagina
        );

        $searchService->storeSearchInSession($busqueda);

        if($searchForm->isSubmitted() && $searchForm->isValid())
            return $this->redirectToRoute('ticket_list', ['id' => $participant->getId()]);
    
        return $this->renderForm('ticket/list.html.twig', [
            'search' => $searchForm,
            'paginator' => $paginator,
            'tickets' => $tickets,
            'participant' => $participant
        ]);
    }



    #[Route('/create/{participant<\d+>}/{activity<\d+>}', name: 'create_in_activity_after_participant')]
    public function create(
            Participant $participant,
            Activity $activity,
            ActivityRepository $activityRepository,
            TicketRepository $ticketRepository,
            ParticipantRepository $participantRepository,
            TicketStatusRepository $ticketStatusRepository,
    ): Response
    {
        //denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $ticket = new Ticket();
        $ticketStatus = $ticketStatusRepository->find(GetTicketStatus::OPEN);
        
        $ticket->setParticipant($participant)
                ->setActivity($activity)
                ->setTicketStatus($ticketStatus)
                ->setIsDeleted(0);

        $activity->setPlacesTaken($activity->getPlacesTaken() + 1);

        $activityRepository->add($activity, true);
        $ticketRepository->add($ticket, true);
    
        return $this->redirectToRoute('activity_show', ['id' => $activity->getId()]);

    }


    /**
     * @Route("/afegir/participant/{id<\d+>}", 
     * name="create_in_activity")
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


    $ticket->setParticipant($participant);
    $ticket->setActivity($activity);
    $ticket->setTicketStatus($ticketStatus);
    $activity->setPlacesTaken($activity->getPlacesTaken() + 1);

    $activityRepository->add($activity, true);
    $ticketRepository->add($ticket, true);

    return $this->redirectToRoute('activity_show', ['id' => $activity->getId()]);
}


  /**
     * @Route("/search/forget", name="forget_search")
     */     
    public function forgetSearch(
        SimpleSearchService $searchService
        ): Response
    {
        $searchService->removeSearchFromSession(Ticket::class);
        $this->addFlash('success', 'Filtre eliminat.');
        return $this->redirectToRoute('participant_list');
    }
}


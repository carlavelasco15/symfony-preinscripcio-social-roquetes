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
use App\Security\EmailVerifier;
use App\Services\SimpleSearchService;
use App\Services\PaginatorService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tiquet', name: 'ticket_')]
class TicketController extends AbstractController
{

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }


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



    #[Route('/create/participant/{participant<\d+>}/activitat/{activity<\d+>}', name: 'create_in_activity_after_participant')]
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
        $ticketsOnActivityList = $ticketRepository->countTicketsPerActivity($activity);
        $entity = $activity->getEntity();
        $ticket->setParticipant($participant)
                ->setActivity($activity)
                ->setTicketStatus($ticketStatus)
                ->setIsDeleted(0);
        $boolWaitingList = $ticketsOnActivityList < $activity->getPlacesTotal() ? 0 : 1;
        $ticket->setIsWaitingList($boolWaitingList)
                ->setUser($this->getUser());
        $ticketRepository->add($ticket, true);
        $activity->setPlacesTaken($ticketRepository->countTicketsPerActivity($activity));
        $activityRepository->add($activity, true);
        foreach ($entity->getUserEntities() as $userEntity) {
            $user = $userEntity->getUser();
            if ($user->isEmailEntNewPrescription() ) {
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $this->getUser(),
                    (new TemplatedEmail())
                        ->from(new Address('noreply@prescripciosocialroquetes.cat', 'Registre d\'usuari'))
                        ->to($user->getEmail())
                        ->subject('Nova prescripció a ' . $activity->getName())
                        ->context([
                            'ticket' => $ticket,
                            'activity' => $activity,
                            'participant' => $participant
                        ])
                        ->htmlTemplate('email/prescription_equipment.html.twig')
                );
            }
        }
    
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
        $entity = $activity->getEntity();
        $ticket = new Ticket();
        $ticketStatus = $ticketStatusRepository->find(GetTicketStatus::OPEN);
        $ticketsOnActivityList = $ticketRepository->countTicketsPerActivity($activity);
        $ticket->setParticipant($participant)
                ->setActivity($activity)
                ->setTicketStatus($ticketStatus)
                ->setIsDeleted(0);
        $boolWaitingList = $ticketsOnActivityList < $activity->getPlacesTotal() ? 0 : 1;
        $ticket->setIsWaitingList($boolWaitingList)
                ->setUser($this->getUser());
        $ticketRepository->add($ticket, true);
        $activity->setPlacesTaken($ticketRepository->countTicketsPerActivity($activity));
        $activityRepository->add($activity, true);
        if($participant->getEmail()) {
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $this->getUser(),
                (new TemplatedEmail())
                    ->from(new Address('noreply@prescripciosocialroquetes.cat', 'Registre d\'usuari'))
                    ->to($participant->getEmail())
                    ->subject('Preinscripció a ' . $activity->getName())
                    ->context([
                        'ticket' => $ticket,
                        'activity' => $activity,
                        'entity' => $entity
                    ])
                    ->htmlTemplate('email/prescription_participant.html.twig')
            );
        }
        foreach ($entity->getUserEntities() as $userEntity) {
            $user = $userEntity->getUser();
            if ($user->isEmailEntNewPrescription() ) {
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $this->getUser(),
                        (new TemplatedEmail())
                            ->from(new Address('noreply@prescripciosocialroquetes.cat', 'Registre d\'usuari'))
                            ->to($user->getEmail())
                            ->subject('Nova prescripció a ' . $activity->getName())
                            ->context([
                                'ticket' => $ticket,
                                'activity' => $activity,
                                'participant' => $participant
                            ])
                            ->htmlTemplate('email/prescription_equipment.html.twig')
                    );
                }
            }
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


    /**
     * @Route("/espera/{id<\d+>}", name="toggle_waiting_list")
     */     
    public function toggleWaitingList(
        Ticket $ticket,
        TicketRepository $ticketRepository,
        ActivityRepository $activityRepository
        ): Response
    {
        $activity = $ticket->getActivity();
        $isInWaitingList =  $ticket->isIsWaitingList() ? 0 : 1;
        $ticket->setIsWaitingList($isInWaitingList);
        $ticketRepository->add($ticket, true);
        $mensaje = $isInWaitingList ? "El/la participant " . $ticket->getParticipant()->getName() . " ara es troba a la llista d'espera." : "El/la participant " . $ticket->getParticipant()->getName() . " ara es troba a la llista de partticipants.";
        $this->addFlash('success', $mensaje);
        $activity->setPlacesTaken($ticketRepository->countTicketsPerActivity($activity));
        $activityRepository->add($activity, true);
       
        return $this->redirectToRoute('activity_show', ['id' => $activity->getId()]);
    }


    /**
     * @Route("/estat/{id<\d+>}", name="toggle_ticket_status")
     */     
    public function toggleTicketStatus(
        Ticket $ticket,
        TicketRepository $ticketRepository,
        ActivityRepository $activityRepository,
        TicketStatusRepository $ticketStatusRepository
        ): Response
    {
        $ticketStatus = $ticket->getTicketStatus();
        $newTicketStatusId =  ($ticketStatus->getId() == GetTicketStatus::OPEN) ?  GetTicketStatus::ACTIVE : GetTicketStatus::OPEN;
        $newTicketStatus = $ticketStatusRepository->find($newTicketStatusId);
        $ticket->setTicketStatus($newTicketStatus);
        $ticketRepository->add($ticket, true);
       
        return $this->redirectToRoute('activity_show', ['id' => $ticket->getActivity()->getId()]);
    }


    /**
     * @Route("/eliminar/{id<\d+>}", name="delete")
     */
    public function delete(
                Ticket $ticket,
                TicketRepository $ticketRepository,
                ActivityRepository $activityRepository
            ): Response
    {
        $activity = $ticket->getActivity();
        $this->addFlash('success', "El ticket a l'activitat " . $activity->getName() . " s'ha eliminat correctament.");
        $ticketRepository->remove($ticket, true);
        $activity->setPlacesTaken($ticketRepository->countTicketsPerActivity($activity));
        $activityRepository->add($activity, true);
        
        return $this->redirectToRoute('ticket_list', ['id' => $ticket->getParticipant()->getId()]);
    }
}


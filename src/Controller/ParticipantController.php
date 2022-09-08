<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Search;
use App\Form\ParticipantFormType;
use App\Form\SearchFormType;
use App\Repository\ParticipantRepository;
use App\Repository\UserEntityRepository;
use App\Services\SimpleSearchService;
use App\Services\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/participant', name: 'participant_')]
class ParticipantController extends AbstractController
{
    
    #[Route('s/{pagina}', defaults: ['pagina' => 1], name: 'list')]
    public function list(
                int $pagina,
                Request $request,
                PaginatorService $paginator,
                SimpleSearchService $searchService,
                ParticipantRepository $participantRepository
                ):Response 
    {
        $this->denyAccessUnlessGranted('see', (new Participant));
        $busqueda = new Search();
        $busqueda->setEntity(Participant::class);
        $busqueda = $searchService->getSearchFromSession(Participant::class) ?? $busqueda;
        $searchForm = $this->createForm(SearchFormType::class, $busqueda, [
            'field_choices' => [
                'Nom' => 'name',
                'DNI' => 'dni',
            ],
            'order_choices' => [
                'ID' => 'id',
                'Nom' => 'name',
            ]
        ]);
        $searchForm->handleRequest($request);
        $searchService->setSearch($busqueda);
        $participants = $paginator->paginate(
            $participantRepository->participantsPerEntity($busqueda),
            $pagina
        );
        $searchService->storeSearchInSession($busqueda);
        if($searchForm->isSubmitted() && $searchForm->isValid())
            return $this->redirectToRoute('participant_list');
        return $this->renderForm('participant/list.html.twig', [
            'search' => $searchForm,
            'paginator' => $paginator,
            'participants' => $participants
        ]);
    }

    
    #[Route('/crear', name: 'create')]
    public function create(
                Request $request,
                ParticipantRepository $participantRepository,
                UserEntityRepository $userEntityRepository,
            ): Response
    {
        $participant = new Participant();
        $this->denyAccessUnlessGranted('create', $participant);
        $user_entity_id = $request->getSession()->get('user_entity_id');
        $entity = $userEntityRepository->find($user_entity_id)->getEntity();
        $form = $this->createForm(ParticipantFormType::class, $participant);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', "Participant ". $participant->getName() ." amb éxit.");
            $participant->setEntity($entity);
            $participantRepository->add($participant, true);
            return $this->redirectToRoute('participant_list');
        }
        return $this->renderForm('participant/create.html.twig', [
            'formulario' => $form
        ]);
    }

    
    #[Route('/editar/{id<\d+>}', name: 'edit')]
    public function edit(
                Participant $participant,
                Request $request,
                ParticipantRepository $participantRepository
            ): Response
    {
        $this->denyAccessUnlessGranted('edit', $participant);
        $form = $this->createForm(ParticipantFormType::class, $participant);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', "Participant ". $participant->getName() ." editat amb éxit.");
            $participantRepository->add($participant, true);
            return $this->redirectToRoute('participant_list');
        }
        return $this->renderForm('participant/edit.html.twig', [
            'formulario' => $form,
            'participant' => $participant
        ]);
    }

    
    #[Route('/create/tiquet', name: 'create_with_ticket')]
    public function createWithTiquet(
                Request $request,
                ParticipantRepository $participantRepository,
                UserEntityRepository $userEntityRepository,
            ): Response
    {
        $participant = new Participant();
        $this->denyAccessUnlessGranted('create', $participant);
        $activity_id = $request->request->get('activity_id');
        $user_entity_id = $request->getSession()->get('user_entity_id');
        $entity = $userEntityRepository->find($user_entity_id)->getEntity();
        $form = $this->createForm(ParticipantFormType::class, $participant);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $participant->setEntity($entity);
            $participantRepository->add($participant, true);
            return $this->redirectToRoute('ticket_create_in_activity_after_participant', ['participant' => $participant->getId(), 'activity' => (int) $activity_id]);
        }
    }


    #[Route('/eliminar/{id}', name: 'delete')]
    public function delete(
                Participant $participant,
                ParticipantRepository $participantRepository
            ): Response
    {
        $this->denyAccessUnlessGranted('delete', $participant);
        $this->addFlash('success', "El participant " . $participant->getName() . " s'ha eliminat correctament.");
        $participantRepository->remove($participant, true);
        return $this->redirectToRoute('participant_list');
    }


    #[Route('/search/forget', name: 'forget_search')]    
    public function forgetSearch(
        SimpleSearchService $searchService
        ): Response
    {
        $searchService->removeSearchFromSession(Participant::class);
        $this->addFlash('success', 'Filtre eliminat.');
        return $this->redirectToRoute('participant_list');
    }

}




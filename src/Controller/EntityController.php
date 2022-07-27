<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\Search;
use App\Form\EntityFormType;
use App\Form\SearchFormType;
use App\Repository\EntityRepository;
use App\Services\SimpleSearchService;
use App\Services\FileService;
use App\Services\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entitat', name: 'entity_')]
class EntityController extends AbstractController
{
  
    #[Route('s/{pagina}', defaults: ['pagina' => 1], name: 'list')]
    public function list(int $pagina,
            Request $request,
            PaginatorService $paginator,
            SimpleSearchService $searchService): Response
    {

        $busqueda = new Search();
        $busqueda->setEntity(Entity::class);

        $busqueda = $searchService->getSearchFromSession(Entity::class) ?? $busqueda;

        $searchForm = $this->createForm(SearchFormType::class, $busqueda, [
            'field_choices' => [
                'Nom' => 'name'
            ],
            'order_choices' => [
                'ID' => 'id'
            ]
            ]);

        $searchForm->handleRequest($request);
        $searchService->setSearch($busqueda);

        $entities = $paginator->paginate(
            $searchService->prepareQuery(),
            $pagina
        );

        $searchService->storeSearchInSession($busqueda);

        if($searchForm->isSubmitted() && $searchForm->isValid())
            return $this->redirectToRoute('entity_list');


        return $this->renderForm('entity/list.html.twig', [
            'search' => $searchForm,
            'paginator' => $paginator,
            'entities' => $entities
        ]);
    }


    #[Route('/crear', name: 'create')]
    public function create(
        Request $request,
        FileService $uploader,
        EntityRepository $entityRepository
    ): Response
    {

        $entity = new Entity();

        $form = $this->createForm(EntityFormType::class, $entity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('picture')->getData();
            
            $uploader->setTargetDirectory($this->getParameter('app.entity_images.root'));

            if($file)
                $entity->setPicture($uploader->upload($file));

            $entity->setCreatedAt(new \DateTimeImmutable());

            $entityRepository->add($entity, true);
            return $this->redirectToRoute('entity_list');
        }

        return $this->render('entity/create.html.twig', [
            'formulario' => $form->createView(),
        ]);
    }



    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        return $this->render('entity/index.html.twig', [
            'controller_name' => 'EntityController',
        ]);
    } 


   
    #[Route('/imatge/{picture}', name: 'picture')]
    public function showPicture(string $picture): Response
    {
        
        //denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $ruta = $this->getParameter('app.entity_images.root');

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

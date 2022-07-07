<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Search;
use App\Service\SimpleSearchService;
use App\Services\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestSimpleSearchServiceController extends AbstractController
{
    #[Route('/test/search/{campo}/{valor}/{orden}/{sentido}/{limite}', name: 'test_search')]
    public function prova1(
        SimpleSearchService $searchService,
        string $campo = 'id',
        string $valor = '',
        string $orden = 'id',
        string $sentido = 'ASC',
        int $limite = 100
    ): Response
    {
        $busqueda = new Search();
        $busqueda->setEntity(Activity::class)
                ->setField($campo)
                ->setValue($valor)
                ->setOrderField($orden)
                ->setOrder($sentido)
                ->setLimit($limite);


        $searchService->setSearch($busqueda);

        $activities = $searchService->find();

        $texto = '';
        foreach($activities as $activity)
            $texto .= $activity->getName(). '<br>';

        return new Response($texto);
    }

    #[Route('/test/search/pag/{pagina}', name: 'test_search')]
    public function prova2(
        SimpleSearchService $searchService,
        PaginatorService $paginator,
        int $pagina = 1
    ): Response
    {

        $paginator->setLimit(3);

        $busqueda = new Search();
        $busqueda->setEntity(Activity::class)
                ->setField('name')
                ->setValue('Act')
                ->setOrderField('id')
                ->setOrder('ASC');


        $searchService->setSearch($busqueda);

        $paginatorResult = $paginator->paginate(
            $searchService->prepareQuery(), $pagina
        );

        $texto = '';
        foreach($paginatorResult as $activity)
            $texto .= $activity->getName(). '<br>';

        return new Response($texto);
    }
}

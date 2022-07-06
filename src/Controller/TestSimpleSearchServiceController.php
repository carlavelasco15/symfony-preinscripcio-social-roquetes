<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Search;
use App\Service\SimpleSearchService;
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
}

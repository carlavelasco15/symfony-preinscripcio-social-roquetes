<?php
namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

use App\Entity\Search;
use Symfony\Component\HttpFoundation\RequestStack;


class SimpleSearchService {

    private $search = NULL;
    private $entityManager;
    private $session;

    public function __construct (
            EntityManagerInterface $entityManager,
            RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->session = $requestStack->getSession();
    }

    public function setSearch(Search $search):self {
        $this->search = $search;
        return $this;
    }

    public function getSearch(): ?Search {
        return $this->search;
    }

    /* TODO */
    public function prepareQuery(): Query {
        $consulta = $this->entityManager->createQuery(
            "SELECT p
            FROM ".$this->search->getEntity()." p
            WHERE p.".$this->search->getField()." LIKE :valor
            ORDER BY p.id ASC")

        ->setParameter('valor', '%'. $this->search->getValue().'%')
        ->setMaxResults($this->search->getLimit());

        return $consulta;
    }

    public function storeSearchInSession(Search $search) {
        $this->session->set("Search_".$search->getEntity().$search->getRole(), $search);
    }

    public function getSearchFromSession(string $entity, string $role = NULL): ?Search{
        if($role)
            return $this->session->get("Search_".$entity.$role);
        return $this->session->get("Search_".$entity);
    }

    public function removeSearchFromSession(string $entity) {
        $this->session->remove("Search_".$entity);
    }

}
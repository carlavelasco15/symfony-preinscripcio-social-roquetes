<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    private $requestStack, $entityRepository, $userEntityRepository;

    public function __construct(ManagerRegistry $registry,
                                RequestStack $requestStack,
                                UserEntityRepository $userEntityRepository,
                                EntityRepository $entityRepository,)
    {
        parent::__construct($registry, Activity::class);
        $this->requestStack = $requestStack;
        $this->userEntityRepository = $userEntityRepository;
        $this->entityRepository = $entityRepository;
    }

    public function add(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchActivityQueryPrescriptor($search): Query {
        $consulta = $this->getEntityManager()->createQuery(
            "SELECT p
            FROM App\Entity\Activity p
            WHERE p.".$search->getField()." LIKE :valor
            AND p.is_deleted = 0
            AND p.is_visible = 1
            ORDER BY p.id ASC")

        ->setParameter('valor', '%'. $search->getValue() .'%')
        ->setMaxResults($search->getLimit());

        return $consulta;
    }


    public function searchActivityQueryEditor($search): Query {

        if($user_entity_id = $this->requestStack->getSession()->get('user_entity_id')) 
            $entity_id = $this->userEntityRepository->find($user_entity_id)->getEntity()->getId();

        $consulta = $this->getEntityManager()->createQuery(
            "SELECT p
            FROM App\Entity\Activity p
            WHERE p.".$search->getField()." LIKE :valor
            AND p.entity = ".$entity_id."
            AND p.is_deleted = 0
            ORDER BY p.id ASC")

        ->setParameter('valor', '%'. $search->getValue() .'%')
        ->setMaxResults($search->getLimit());

        return $consulta;
    }


    public function searchActivityQueryEditorAndPrescriptor($search): array {
       
        $editor = $this->searchActivityQueryEditor($search);
        $prescriptor = $this->searchActivityQueryPrescriptor($search);

        return array_merge($prescriptor->getResult(), $editor->getResult());
    }

}

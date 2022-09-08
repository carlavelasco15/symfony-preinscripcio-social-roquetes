<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<Participant>
 *
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository
{
    private $requestStack, $entityRepository, $userEntityRepository;

    public function __construct(ManagerRegistry $registry,
                                RequestStack $requestStack,
                                UserEntityRepository $userEntityRepository,
                                EntityRepository $entityRepository)
    {
        parent::__construct($registry, Participant::class);
        $this->requestStack = $requestStack;
        $this->userEntityRepository = $userEntityRepository;
        $this->entityRepository = $entityRepository;
    }

    public function add(Participant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Participant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function participantsPerEntity($search): Query {

        if($user_entity_id = $this->requestStack->getSession()->get('user_entity_id')) 
            $entity_id = $this->userEntityRepository->find($user_entity_id)->getEntity()->getId();

        $consulta = $this->getEntityManager()->createQuery(
            "SELECT p
            FROM App\Entity\Participant p
            WHERE p.".$search->getField()." LIKE :valor
            AND p.entity = ".$entity_id."
            ORDER BY p.id ASC")

        ->setParameter('valor', '%'. $search->getValue().'%')
        ->setMaxResults($search->getLimit());

        return $consulta;
    }

}

<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function add(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchTicketsByParticipant($search): Query {
        $consulta = $this->getEntityManager()->createQuery(
            "SELECT p
            FROM App\Entity\Ticket p
            WHERE p.activity in (
                SELECT b
                FROM App\Entity\Activity b
                WHERE b.name LIKE :valor
            )
            AND p.participant = :id
            ORDER BY p.id ASC"
        )

        
        ->setParameter('id', $search->getEntityId())
        ->setParameter('valor',  '%'. $search->getValue() .'%')
        ->setMaxResults($search->getLimit());


        return $consulta;
    }


    public function ticketsPerActivity($activity): array {
        $consulta = $this->getEntityManager()->createQuery(
            "SELECT p
            FROM App\Entity\Ticket p
            WHERE p.activity = :id
            AND p.is_deleted = 0
            AND p.is_waiting_list = 0
            ORDER BY p.id ASC"
        )
        ->setParameter('id', $activity)
        ->getResult();

        return $consulta;
    }

    public function ticketsWaitingListPerActivity($activity): array {
        $consulta = $this->getEntityManager()->createQuery(
            "SELECT p
            FROM App\Entity\Ticket p
            WHERE p.activity = :id
            AND p.is_deleted = 0
            AND p.is_waiting_list = 1
            ORDER BY p.id ASC"
        )
        ->setParameter('id', $activity)
        ->getResult();

        return $consulta;
    }

    public function countTicketsPerActivity($activity) {
        $consulta = $this->getEntityManager()->createQuery(
            "SELECT count(p)
            FROM App\Entity\Ticket p
            WHERE p.activity = :id
            AND p.is_deleted = 0
            AND p.is_waiting_list = 0"
        )

        ->setParameter('id', $activity)
        ->getSingleScalarResult();

        return $consulta;
    }

}

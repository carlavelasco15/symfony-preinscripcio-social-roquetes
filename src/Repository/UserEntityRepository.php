<?php

namespace App\Repository;

use App\Entity\UserEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserEntity>
 *
 * @method UserEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEntity[]    findAll()
 * @method UserEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEntity::class);
    }

    public function add(UserEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAccountModelByAccountIdAndUserId(int $entity_id, int $user_id) {
        $consulta = $this->getEntityManager()->createQuery(
            "SELECT u
            FROM App\Entity\UserEntity u
            WHERE u.user = :user_id
            AND u.entity = :entity_id"
        )

        ->setParameter('entity_id', $entity_id)
        ->setParameter('user_id',  $user_id)
        ->getSingleResult();

        return $consulta;
    }

}

<?php

namespace App\Repository;

use App\Entity\EntityManagerProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EntityManagerProvider>
 *
 * @method EntityManagerProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntityManagerProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntityManagerProvider[]    findAll()
 * @method EntityManagerProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntityManagerProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntityManagerProvider::class);
    }

//    /**
//     * @return EntityManagerProvider[] Returns an array of EntityManagerProvider objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EntityManagerProvider
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

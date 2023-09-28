<?php

namespace App\Repository;

use App\Entity\MyFavoriteTypesOfPerfumes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MyFavoriteTypesOfPerfumes>
 *
 * @method MyFavoriteTypesOfPerfumes|null find($id, $lockMode = null, $lockVersion = null)
 * @method MyFavoriteTypesOfPerfumes|null findOneBy(array $criteria, array $orderBy = null)
 * @method MyFavoriteTypesOfPerfumes[]    findAll()
 * @method MyFavoriteTypesOfPerfumes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MyFavoriteTypesOfPerfumesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MyFavoriteTypesOfPerfumes::class);
    }

    public function save(MyFavoriteTypesOfPerfumes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MyFavoriteTypesOfPerfumes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MyFavoriteTypesOfPerfumes[] Returns an array of MyFavoriteTypesOfPerfumes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MyFavoriteTypesOfPerfumes
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

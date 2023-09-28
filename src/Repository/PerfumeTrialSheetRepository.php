<?php

namespace App\Repository;

use App\Entity\PerfumeTrialSheet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PerfumeTrialSheet>
 *
 * @method PerfumeTrialSheet|null find($id, $lockMode = null, $lockVersion = null)
 * @method PerfumeTrialSheet|null findOneBy(array $criteria, array $orderBy = null)
 * @method PerfumeTrialSheet[]    findAll()
 * @method PerfumeTrialSheet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PerfumeTrialSheetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PerfumeTrialSheet::class);
    }

    public function save(PerfumeTrialSheet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PerfumeTrialSheet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PerfumeTrialSheet[] Returns an array of PerfumeTrialSheet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PerfumeTrialSheet
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

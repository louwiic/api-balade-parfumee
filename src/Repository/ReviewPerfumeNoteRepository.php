<?php

namespace App\Repository;

use App\Entity\ReviewPerfumeNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReviewPerfumeNote>
 *
 * @method ReviewPerfumeNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewPerfumeNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewPerfumeNote[]    findAll()
 * @method ReviewPerfumeNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewPerfumeNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewPerfumeNote::class);
    }

    public function save(ReviewPerfumeNote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReviewPerfumeNote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReviewPerfumeNote[] Returns an array of ReviewPerfumeNote objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReviewPerfumeNote
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

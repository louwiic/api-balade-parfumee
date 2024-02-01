<?php

namespace App\Repository;

use App\Entity\NotificationsUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationsUsers>
 *
 * @method NotificationsUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationsUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationsUsers[]    findAll()
 * @method NotificationsUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationsUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationsUsers::class);
    }

    /*  public function markNotificationAsRead(int $notificationId, int $userId): void
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $notificationId)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        ->join('nu.user_id', 'u')
            ->where('nu.notification_id = :notificationId')
            ->andWhere('u.id = :userId')
            ->setParameter('notificationId', $notificationId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        if ($notificationUser) {
             $notificationUser->setIsRead(true);

            // Enregistrement des modifications en base de données
            $entityManager = $this->getEntityManager();
            $entityManager->persist($notificationUser);
            $entityManager->flush();
        } else {
            // La notification n'existe pas ou n'est pas associée à l'utilisateur, gérer l'erreur selon vos besoins
            throw new \Exception('Notification not found or not associated with the user.');
        }
    } */

    public function findIsReadNotificationsForUser(int $userId): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT nu.id, nu.isRead, u.firstName
             FROM App\Entity\NotificationsUsers nu
             JOIN nu.user_id u
             WHERE nu.isRead = true AND u.id = :userId'
        )->setParameter('userId', $userId);

        return $query->getResult();
    }

    //    public function findOneBySomeField($value): ?NotificationsUsers
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function add(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUserTransactionsByFilters($user, array $filters): array
    {
        $query = $this->createQueryBuilder('t')
            ->leftJoin('t.Course', 'c')
            ->andWhere('t.billing_user = :id')
            ->setParameter('id', $user->getId())
            ->orderBy('t.Datetime_transaction', 'DESC');

        if ($filters['type']) {
            $query->andWhere('t.type = :type')
                ->setParameter('type', $filters['type']);
        }

        if ($filters['course_code']) {
            $query->andWhere('c.char_code = :cc')
                ->setParameter('cc', $filters['course_code']);
        }

        if ($filters['skip_expired']) {
            $query->andWhere('t.End_datetime IS NULL OR t.End_datetime >= :today')
                ->setParameter('today', new \DateTimeImmutable());
        }

        return $query->getQuery()->getResult();
    }
//    /**
//     * @return Transaction[] Returns an array of Transaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transaction
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

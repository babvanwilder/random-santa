<?php

namespace App\Repository;

use App\Entity\Santa;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Santa>
 *
 * @method Santa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Santa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Santa[]    findAll()
 * @method Santa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SantaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Santa::class);
    }

    public function save(Santa $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Santa $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOpen(): array
    {
        $queryBuilder = $this->createQueryBuilder('santa');
        $expr = $queryBuilder->expr();

        return $queryBuilder
            ->andWhere($expr->between(':now', 'santa.dateStart', 'santa.dateClose'))
            ->andWhere($expr->isNull('santa.dateArchived'))
            ->setParameters([
                'now' => new DateTimeImmutable()
            ])
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFuture(): array
    {
        $queryBuilder = $this->createQueryBuilder('santa');
        $expr = $queryBuilder->expr();

        return $queryBuilder
            ->andWhere($expr->gte('santa.dateStart', ':now'))
            ->andWhere($expr->isNull('santa.dateArchived'))
            ->setParameters([
                'now' => new DateTimeImmutable()
            ])
            ->getQuery()
            ->getResult()
        ;
    }

    public function findClose(): array
    {
        $queryBuilder = $this->createQueryBuilder('santa');
        $expr = $queryBuilder->expr();

        return $queryBuilder
            ->andWhere($expr->lt('santa.dateClose', ':now'))
            ->andWhere($expr->isNull('santa.dateArchived'))
            ->setParameters([
                'now' => new DateTimeImmutable()
            ])
            ->getQuery()
            ->getResult()
        ;
    }

    public function findArchived(): array
    {
        $queryBuilder = $this->createQueryBuilder('santa');
        $expr = $queryBuilder->expr();

        return $queryBuilder
            ->andWhere($expr->isNotNull('santa.dateArchived'))
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Santa[] Returns an array of Santa objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Santa
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

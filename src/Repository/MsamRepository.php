<?php

namespace App\Repository;

use App\Entity\Msam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Msam>
 *
 * @method Msam|null find($id, $lockMode = null, $lockVersion = null)
 * @method Msam|null findOneBy(array $criteria, array $orderBy = null)
 * @method Msam[]    findAll()
 * @method Msam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MsamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Msam::class);
    }

    public function save(Msam $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Msam $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAmountMsam(): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Msam[] Returns an array of Msam objects
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

//    public function findOneBySomeField($value): ?Msam
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Commutator;
use App\Repository\Traits\PaginateTarit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commutator>
 *
 * @method Commutator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commutator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commutator[]    findAll()
 * @method Commutator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommutatorRepository extends ServiceEntityRepository
{
    use PaginateTarit;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commutator::class);
    }

    public function save(Commutator $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commutator $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findCommutator(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c');
        if($filter){
            $builder->andWhere('c.physicalAddress LIKE :filter')
                ->orWhere('c.brand LIKE :filter')
                ->orWhere('c.contic LIKE :filter')
                ->orWhere('c.gateway LIKE :filter')
                ->orWhere('c.inventory LIKE :filter')
                ->orWhere('c.ip LIKE :filter')
                ->orWhere('c.model LIKE :filter')
                ->orWhere('c.physicalSerial LIKE :filter')
                ->setParameters([
                    ':filer' => '%'.$filter.'%',
                ]);
        }

        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

//    /**
//     * @return Commutator[] Returns an array of Commutator objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commutator
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

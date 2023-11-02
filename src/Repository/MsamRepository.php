<?php

namespace App\Repository;

use App\Entity\Enums\State;
use App\Entity\Msam;
use App\Entity\Port;
use App\Repository\Traits\PaginateTarit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
    use PaginateTarit;

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

    private function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if($filter){
            $predicate = "m.brand LIKE :filter ";
            $predicate .= "OR m.contic LIKE :filter ";
            $predicate .= "OR m.inventory LIKE :filter ";
            $predicate .= "OR m.ip LIKE :filter ";
            $predicate .= "OR m.model LIKE :filter ";
            $predicate .= "OR m.physicalSerial LIKE :filter ";
            $predicate .= "OR m.physicalAddress LIKE :filter ";
            if($place){
                $predicate .= "OR mun.name LIKE :filter ";
                $predicate .= "OR pro.name LIKE :filter ";
            }

            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findMsams(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')->select(['m', 'mun', 'pro'])
            ->innerJoin('m.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('m.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findMsamByPort(Port $port, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')->select(['m', 'mun', 'pro'])
            ->innerJoin('m.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
        $builder->leftJoin('m.port', 'port');
        $builder->andWhere($builder->expr()->in('port.id', $port->getId()));
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('m.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findInactiveMsamWithoutPort(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')->select(['m', 'mun', 'pro'])
            ->innerJoin('m.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->where('m.state = :state')
            ->setParameter(':state', State::Inactive)
            ->andWhere('m.port IS NULL');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('m.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
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

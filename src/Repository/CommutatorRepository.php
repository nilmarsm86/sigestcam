<?php

namespace App\Repository;

use App\Entity\Commutator;
use App\Entity\Enums\State;
use App\Entity\Port;
use App\Repository\Traits\PaginateTarit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
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

    private function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if($filter){
            $predicate = "c.multicast LIKE :filter ";
            $predicate .= "OR c.gateway LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR c.brand LIKE :filter ";
            $predicate .= "OR c.brand LIKE :filter ";
            $predicate .= "OR c.physicalSerial LIKE :filter ";
            $predicate .= "OR c.model LIKE :filter ";
            $predicate .= "OR c.inventory LIKE :filter ";
            $predicate .= "OR c.contic LIKE :filter ";
            $predicate .= "OR c.physicalAddress LIKE :filter ";
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
    public function findCommutator(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
            //->andWhere('c.gateway IS NOT NULL');
            //->andWhere('c.multicast IS NOT NULL');
        /*if($filter){
            $predicate = "c.multicast LIKE :filter ";
            $predicate .= "OR c.gateway LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR m.name LIKE :filter ";
            $predicate .= "OR p.name LIKE :filter ";

            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }*/
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findMasterCommutator(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->andWhere('c.gateway IS NOT NULL');
        //->andWhere('c.multicast IS NOT NULL');
        /*if($filter){
            $predicate = "c.multicast LIKE :filter ";
            $predicate .= "OR c.gateway LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR m.name LIKE :filter ";
            $predicate .= "OR p.name LIKE :filter ";

            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }*/
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findByPort(Port $port, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
        $builder->leftJoin('c.port', 'port');
        $builder->andWhere($builder->expr()->in('port.id', $port->getId()));
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findInactiveWithoutPort(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->where('c.state = :state')
            ->setParameter(':state', State::Inactive)
            ->andWhere('c.port IS NULL');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findActiveAndSlave(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            //->innerJoin('c.masterCommutator', 'masterC')
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->where('c.state = :state')
            ->setParameter(':state', State::Active)
            ->andWhere('c.port IS NULL')
            ->andWhere('c.masterCommutator IS NULL')
            ->andWhere('c.gateway IS NULL')
            ->andWhere('c.multicast IS NULL');

        $this->addFilter($builder, $filter);
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

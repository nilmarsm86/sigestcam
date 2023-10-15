<?php

namespace App\Repository;

use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Enums\State;
use App\Entity\Modem;
use App\Entity\Port;
use App\Repository\Traits\PaginateTarit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Modem>
 *
 * @method Modem|null find($id, $lockMode = null, $lockVersion = null)
 * @method Modem|null findOneBy(array $criteria, array $orderBy = null)
 * @method Modem[]    findAll()
 * @method Modem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModemRepository extends ServiceEntityRepository
{
    use PaginateTarit;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modem::class);
    }

    public function save(Modem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Modem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Modem[] Returns an array of Modem objects
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

//    public function findOneBySomeField($value): ?Modem
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    private function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if($filter){
            $predicate = "m.brand LIKE :filter ";
            $predicate .= "OR m.contic LIKE :filter ";
            $predicate .= "OR m.inventory LIKE :filter ";
            $predicate .= "OR m.ip LIKE :filter ";
            $predicate .= "OR m.model LIKE :filter ";
            $predicate .= "OR m.physicalSerial LIKE :filter ";
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
    public function findModems(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')->select(['m', 'mun', 'pro'])
            ->innerJoin('m.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
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
    public function findActiveModems(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')
            ->where('m.state = :state')
            ->setParameter(':state', State::Active);
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('m.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findModemsWithPortByState(State $state, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')->select(['m', 'mun', 'pro'])
            ->innerJoin('m.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->where('m.state = :state')
            ->setParameter(':state', $state)
            ->andWhere('m.port IS NOT NULL');
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('m.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findActiveModemsWithPort(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        return $this->findModemsWithPortByState(State::Active, $filter, $amountPerPage, $page);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findInactiveModemsWithPort(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        return $this->findModemsWithPortByState(State::Inactive, $filter, $amountPerPage, $page);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findInactiveModems(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')
            ->where('m.state = :state')
            ->setParameter(':state', State::Inactive);
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('m.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findInactiveModemsWithoutPort(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
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

    public function findInactiveModemsWithoutPortAndMasterModem(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')->select(['m', 'mun', 'pro'])
            ->innerJoin('m.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->where('m.state = :state')
            ->setParameter(':state', State::Inactive)
            ->andWhere('m.port IS NULL')
            ->andWhere('m.masterModem IS NULL');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('m.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findModemsByCommutator(Commutator $commutator, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $portsId = [];
        foreach ($commutator->getPorts() as $port){
            $portsId[] = $port->getId();
        }

        $builder = $this->createQueryBuilder('m');
        $builder->leftJoin('m.port', 'port');
        $builder->andWhere($builder->expr()->in('port.id', $portsId));
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('m.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findModemByPort(Port $port, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
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

    public function findModemByMaster(Modem $masterModem, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')->select(['m', 'mun', 'pro'])
            ->innerJoin('m.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
        $builder->leftJoin('m.masterModem', 'master');
        $builder->andWhere($builder->expr()->in('master.id', $masterModem->getId()));
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('m.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findByDirectConnection(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')->select(['m', 'p', 'comm', 'mun', 'pro']);
        $builder->innerJoin('m.port', 'p')
            ->where('p.connectionType = :connectionType')
            ->setParameter(':connectionType', ConnectionType::Direct)
            ->innerJoin('p.commutator', 'comm')
            ->innerJoin('m.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');

        if($filter){
            $predicate = "m.ip LIKE :filter ";
            $predicate .= "OR comm.ip LIKE :filter ";
            $predicate .= "OR mun.name LIKE :filter ";
            $predicate .= "OR pro.name LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
        $query = $builder->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findAmountSlaveModemsByMasterModemId($modemId): array|int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m) as total')
            ->leftJoin('m.masterModem', 'masterModem')
            ->where('masterModem.id = '.$modemId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAmountModem(): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }

}

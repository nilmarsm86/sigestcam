<?php

namespace App\Repository;

use App\Entity\Camera;
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
 * @extends ServiceEntityRepository<Camera>
 *
 * @method Camera|null find($id, $lockMode = null, $lockVersion = null)
 * @method Camera|null findOneBy(array $criteria, array $orderBy = null)
 * @method Camera[]    findAll()
 * @method Camera[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CameraRepository extends ServiceEntityRepository
{
    use PaginateTarit;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Camera::class);
    }

    public function save(Camera $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Camera $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if($filter){
            $predicate = "c.brand LIKE :filter ";
            $predicate .= "OR c.contic LIKE :filter ";
            $predicate .= "OR c.electronicSerial LIKE :filter ";
            $predicate .= "OR c.inventory LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR c.model LIKE :filter ";
            $predicate .= "OR c.physicalSerial LIKE :filter ";
            $predicate .= "OR c.user LIKE :filter ";
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
    public function findCameras(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
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
    public function findActiveCameras(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')
        ->where('c.state = :state')
        ->setParameter(':state', State::Active);
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findCamerasWithPortByState(State $state, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->where('c.state = :state')
            ->setParameter(':state', $state)
            ->andWhere('c.port IS NOT NULL');
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findActiveCamerasWithPort(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        return $this->findCamerasWithPortByState(State::Active, $filter, $amountPerPage, $page);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findInactiveCamerasWithPort(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        return $this->findCamerasWithPortByState(State::Inactive, $filter, $amountPerPage, $page);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findInactiveCameras(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')
            ->where('c.state = :state')
            ->setParameter(':state', State::Inactive);
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findInactiveCamerasWithoutPort(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
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

    public function findInactiveCamerasWithoutPortAndModem(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->where('c.state = :state')
            ->setParameter(':state', State::Inactive)
            ->andWhere('c.port IS NULL')
            ->andWhere('c.modem IS NULL');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findCamerasByCommutator(Commutator $commutator, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $portsId = [];
        foreach ($commutator->getPorts() as $port){
            $portsId[] = $port->getId();
        }

        $builder = $this->createQueryBuilder('c');
        $builder->leftJoin('c.port', 'p');
        $builder->andWhere($builder->expr()->in('p.id', $portsId));
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findCameraByPort(Port $port, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
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

    public function findCameraByPortAndNotModem(Port $port, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
        $builder->leftJoin('c.port', 'port');
        $builder->andWhere($builder->expr()->in('port.id', $port->getId()))
                ->andWhere('c.modem IS NULL');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findCameraByModem(Modem $modem, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'mun', 'pro'])
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
        $builder->leftJoin('c.modem', 'modem');
        $builder->andWhere($builder->expr()->in('modem.id', $modem->getId()));
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findByDirectConnection(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'p', 'comm', 'mun', 'pro']);
        $builder->innerJoin('c.port', 'p')
            ->where('p.connectionType = :connectionType')
            ->setParameter(':connectionType', ConnectionType::Direct)
            ->innerJoin('p.commutator', 'comm')
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->andWhere('c.state = :state')
            ->setParameter(':state', State::Active);

        if($filter){
            $predicate = "c.brand LIKE :filter ";
            $predicate .= "OR c.contic LIKE :filter ";
            $predicate .= "OR c.electronicSerial LIKE :filter ";
            $predicate .= "OR c.inventory LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR c.model LIKE :filter ";
            $predicate .= "OR c.physicalSerial LIKE :filter ";
            $predicate .= "OR c.user LIKE :filter ";
            $predicate .= "OR comm.ip LIKE :filter ";
            $predicate .= "OR mun.name LIKE :filter ";
            $predicate .= "OR pro.name LIKE :filter ";

            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
        $query = $builder->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findBySimpleConnection(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'modem', 'p', 'comm', 'mun', 'pro']);
        $builder->innerJoin('c.modem', 'modem')
            ->innerJoin('modem.port', 'p')
            ->where('p.connectionType = :connectionType')
            ->setParameter(':connectionType', ConnectionType::Simple)
            ->innerJoin('p.commutator', 'comm')
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->andWhere('c.state = :state')
            ->setParameter(':state', State::Active);

        if($filter){
            $predicate = "c.brand LIKE :filter ";
            $predicate .= "OR c.contic LIKE :filter ";
            $predicate .= "OR c.electronicSerial LIKE :filter ";
            $predicate .= "OR c.inventory LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR c.model LIKE :filter ";
            $predicate .= "OR c.physicalSerial LIKE :filter ";
            $predicate .= "OR c.user LIKE :filter ";
            $predicate .= "OR comm.ip LIKE :filter ";
            $predicate .= "OR modem.ip LIKE :filter ";
            $predicate .= "OR mun.name LIKE :filter ";
            $predicate .= "OR pro.name LIKE :filter ";

            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
        $query = $builder->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findBySlaveSwitchConnection(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'slavePort', 'slaveCommutator', 'masterPort', 'masterCommutator', 'mun', 'pro']);
        $builder->innerJoin('c.port', 'slavePort')
            //->where('p.connectionType = :connectionType')
            //->setParameter(':connectionType', ConnectionType::SlaveSwitch)
            ->innerJoin('slavePort.commutator', 'slaveCommutator')
            ->innerJoin('slaveCommutator.port', 'masterPort')
            ->innerJoin('masterPort.commutator', 'masterCommutator')
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->andWhere('c.state = :state')
            ->setParameter(':state', State::Active);

        if($filter){
            $predicate = "c.brand LIKE :filter ";
            $predicate .= "OR c.contic LIKE :filter ";
            $predicate .= "OR c.electronicSerial LIKE :filter ";
            $predicate .= "OR c.inventory LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR c.model LIKE :filter ";
            $predicate .= "OR c.physicalSerial LIKE :filter ";
            $predicate .= "OR c.user LIKE :filter ";
            $predicate .= "OR slaveCommutator.ip LIKE :filter ";
            $predicate .= "OR masterCommutator.ip LIKE :filter ";
            $predicate .= "OR mun.name LIKE :filter ";
            $predicate .= "OR pro.name LIKE :filter ";

            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
        $query = $builder->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findBySlaveModemConnection(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'slaveModem', 'modem', 'port', 'commutator', 'mun', 'pro']);
        $builder->innerJoin('c.modem', 'slaveModem')
            ->innerJoin('slaveModem.masterModem', 'modem')
            ->innerJoin('modem.port', 'port')
            ->innerJoin('port.commutator', 'commutator')
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->where('port.connectionType = :connectionType')
            ->setParameter(':connectionType', ConnectionType::SlaveModem)
            ->andWhere('c.state = :state')
            ->setParameter(':state', State::Active);

        if($filter){
            $predicate = "c.brand LIKE :filter ";
            $predicate .= "OR c.contic LIKE :filter ";
            $predicate .= "OR c.electronicSerial LIKE :filter ";
            $predicate .= "OR c.inventory LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR c.model LIKE :filter ";
            $predicate .= "OR c.physicalSerial LIKE :filter ";
            $predicate .= "OR c.user LIKE :filter ";
            $predicate .= "OR slaveModem.ip LIKE :filter ";
            $predicate .= "OR modem.ip LIKE :filter ";
            $predicate .= "OR commutator.ip LIKE :filter ";
            $predicate .= "OR mun.name LIKE :filter ";
            $predicate .= "OR pro.name LIKE :filter ";

            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
        $query = $builder->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findByFullConnection(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'modem', 'port_card', 'card', 'msam', 'p', 'comm', 'mun', 'pro']);
        $builder->innerJoin('c.modem', 'modem')
            ->innerJoin('modem.port', 'port_card')
            ->innerJoin('port_card.card', 'card')
            ->innerJoin('card.msam', 'msam')
            ->innerJoin('msam.port', 'p')
            ->where('p.connectionType = :connectionType')
            ->setParameter(':connectionType', ConnectionType::Full)
            ->innerJoin('p.commutator', 'comm')
            ->innerJoin('c.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->andWhere('c.state = :state')
            ->setParameter(':state', State::Active);

        if($filter){
            $predicate = "c.brand LIKE :filter ";
            $predicate .= "OR c.contic LIKE :filter ";
            $predicate .= "OR c.electronicSerial LIKE :filter ";
            $predicate .= "OR c.inventory LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR c.model LIKE :filter ";
            $predicate .= "OR c.physicalSerial LIKE :filter ";
            $predicate .= "OR c.user LIKE :filter ";
            $predicate .= "OR modem.ip LIKE :filter ";
            $predicate .= "OR card.name LIKE :filter ";
            $predicate .= "OR msam.ip LIKE :filter ";
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
    public function findAmountCamerasByModemId($modemId): array|int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c) as total')
            ->leftJoin('c.modem', 'modem')
            ->where('modem.id = '.$modemId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAmountCameras(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Camera[] Returns an array of Camera objects
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

//    public function findOneBySomeField($value): ?Camera
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

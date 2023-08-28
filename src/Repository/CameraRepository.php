<?php

namespace App\Repository;

use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Enums\State;
use App\Entity\Port;
use App\Entity\Traits\StateTrait;
use App\Repository\Traits\PaginateTarit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
            $predicate = "c.physicalAddress LIKE :filter ";
            $predicate .= "OR c.brand LIKE :filter ";
            $predicate .= "OR c.contic LIKE :filter ";
            $predicate .= "OR c.electronicSerial LIKE :filter ";
            $predicate .= "OR c.inventory LIKE :filter ";
            $predicate .= "OR c.ip LIKE :filter ";
            $predicate .= "OR c.model LIKE :filter ";
            $predicate .= "OR c.physicalSerial LIKE :filter ";
            $predicate .= "OR c.user LIKE :filter ";
            if($place){
                $predicate .= "OR m.name LIKE :filter ";
                $predicate .= "OR p.name LIKE :filter ";
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
        $builder = $this->createQueryBuilder('c')->select(['c', 'm', 'p'])
            ->innerJoin('c.municipality', 'm')
            ->leftJoin('m.province', 'p');
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
        $builder = $this->createQueryBuilder('c')->select(['c', 'm', 'p'])
            ->innerJoin('c.municipality', 'm')
            ->leftJoin('m.province', 'p')
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
//        $builder = $this->createQueryBuilder('c')
//            ->where('c.state = :state')
//            ->setParameter(':state', State::Active)
//            ->andWhere('c.port IS NOT NULL');
//        $this->addFilter($builder, $filter, false);
//        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
//        return $this->paginate($query, $page, $amountPerPage);

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
//        $builder = $this->createQueryBuilder('c')
//            ->where('c.state = :state')
//            ->setParameter(':state', State::Active)
//            ->andWhere('c.port IS NOT NULL');
//        $this->addFilter($builder, $filter, false);
//        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
//        return $this->paginate($query, $page, $amountPerPage);

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
        $builder = $this->createQueryBuilder('c')->select(['c', 'm', 'p'])
            ->innerJoin('c.municipality', 'm')
            ->leftJoin('m.province', 'p')
            ->where('c.state = :state')
            ->setParameter(':state', State::Inactive)
            ->andWhere('c.port IS NULL');
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
//        $portsId = [];
//        foreach ($commutator->getPorts() as $port){
//            $portsId[] = $port->getId();
//        }

        $builder = $this->createQueryBuilder('c')->select(['c', 'm', 'p'])
            ->innerJoin('c.municipality', 'm')
            ->leftJoin('m.province', 'p');
        $builder->leftJoin('c.port', 'port');
        $builder->andWhere($builder->expr()->in('port.id', $port->getId()));
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findByDirectConnection(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c', 'p', 'comm', 'm', 'pro']);
        $builder->innerJoin('c.port', 'p')
            ->where('p.connectionType = :connectionType')
            ->setParameter(':connectionType', ConnectionType::Direct)
            ->innerJoin('p.commutator', 'comm')
            ->innerJoin('c.municipality', 'm')
            ->leftJoin('m.province', 'pro');

        if($filter){
            $predicate = "c.ip LIKE :filter ";
            $predicate .= "OR comm.ip LIKE :filter ";
            $predicate .= "OR m.name LIKE :filter ";
            $predicate .= "OR pro.name LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
        $query = $builder->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
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

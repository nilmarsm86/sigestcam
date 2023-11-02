<?php

namespace App\Repository;

use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use App\Repository\Traits\PaginateTarit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Port>
 *
 * @method Port|null find($id, $lockMode = null, $lockVersion = null)
 * @method Port|null findOneBy(array $criteria, array $orderBy = null)
 * @method Port[]    findAll()
 * @method Port[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortRepository extends ServiceEntityRepository
{
    use PaginateTarit;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Port::class);
    }

    public function save(Port $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Port $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findAmountConnections(): array|int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p) as total')
            ->where('p.connectionType is not null')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function findAmountByConnectionType(ConnectionType $connectionType): array|int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p) as direct')
            ->where('p.connectionType = :connectionType')
            ->setParameter(':connectionType', $connectionType)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findAmountDirectConnections(): array|int
    {
        return $this->findAmountByConnectionType(ConnectionType::Direct);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findAmountSimpleConnections(): array|int
    {
        return $this->findAmountByConnectionType(ConnectionType::Simple);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findAmountSlaveSwitchConnections(): array|int
    {
        return $this->findAmountByConnectionType(ConnectionType::SlaveSwitch);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findAmountSlaveModemConnections(): array|int
    {
        return $this->findAmountByConnectionType(ConnectionType::SlaveModem);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findAmountFullConnections(): array|int
    {
        return $this->findAmountByConnectionType(ConnectionType::Full);
    }

    public function findByCard(int $cardId, string $filter = '', int $amountPerPage = 20, int $page = 1): Array
    {
        $builder = $this->createQueryBuilder('p')->addSelect(['p']);
        $builder//->where('p.connectionType = :connectionType')
            //->setParameter('connectionType', ConnectionType::Full)
            ->innerJoin('p.card', 'c')
            ->andWhere('c.id = :cardId')
            ->setParameter(':cardId', $cardId);

//            ->join('App\\Entity\\Camera', 'cam', 'with', 'e.id = cam.id');

//        $this->addFiter($builder, $filter);
        $query = $builder->getQuery();
        return $query->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

//    /**
//     * @return PortTrait[] Returns an array of PortTrait objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PortTrait
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

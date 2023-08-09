<?php

namespace App\Repository;

use App\Entity\Camera;
use App\Repository\Traits\PaginateTarit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findCameras(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c');
        if($filter){
            $builder->andWhere('c.physicalAddress LIKE :filter')
                ->orWhere('c.brand LIKE :filter')
                ->orWhere('c.contic LIKE :filter')
                ->orWhere('c.electronicSerial LIKE :filter')
                ->orWhere('c.inventory LIKE :filter')
                ->orWhere('c.ip LIKE :filter')
                ->orWhere('c.model LIKE :filter')
                ->orWhere('c.physicalSerial LIKE :filter')
                ->orWhere('c.user LIKE :filter')
                ->setParameters([
                    ':filter' => '%'.$filter.'%',
                ]);
        }
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
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

<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Msam;
use App\Entity\Port;
use App\Repository\Traits\PaginateTarit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 *
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    use PaginateTarit;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function save(Card $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Card $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if($filter){
            $predicate = "c.name LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findAmountCardsByMsamId($msamId): array|int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c) as total')
            ->leftJoin('c.msam', 'msam')
            ->where('msam.id = '.$msamId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findCardBySlotAndMsam(Msam $msam, int $slot, string $filter = '', int $amountPerPage = 20, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c']);
        $builder->andWhere("c.msam = :msam AND c.slot = :slot ")
                ->setParameter(':msam',$msam)
                ->setParameter(':slot',$slot);
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findCardWithoutSlotAndMsam(string $filter = '', int $amountPerPage = 20, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c')->select(['c']);
        $builder->andWhere("c.msam IS NULL AND c.slot IS NULL ");
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

//    /**
//     * @return Card[] Returns an array of Card objects
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

//    public function findOneBySomeField($value): ?Card
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Enums\InterruptionReason;
use App\Entity\Enums\Priority;
use App\Entity\Enums\ReportState;
use App\Entity\Enums\ReportType;
use App\Entity\Equipment;
use App\Entity\Report;
use App\Repository\Traits\PaginateTarit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Report>
 *
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    use PaginateTarit;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function save(Report $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Report $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if($filter){
            if(str_contains($filter, '/')){
                $predicate = "r.entryDate LIKE :filter ";
                $date = preg_split('/\//', $filter);
                $filter = join('-', array_reverse($date));
            }else{
                $predicate = "r.number LIKE :filter ";
                $predicate .= "OR r.interruptionReason LIKE :filter ";
                $predicate .= "OR e.physicalSerial LIKE :filter ";
                $predicate .= "OR e.ip LIKE :filter ";

                if($place){
                    $predicate .= "OR mun.name LIKE :filter ";
                    $predicate .= "OR pro.name LIKE :filter ";
                }
            }

            $builder->andWhere($predicate)->setParameter(':filter','%'.$filter.'%');
        }
    }

    private function addPriority(QueryBuilder $builder, $priority)
    {
        if($priority){
            $priority = Priority::from($priority);
            $builder->andWhere("r.priority = :priority ")->setParameter(':priority',$priority);
        }
    }

    private function addState(QueryBuilder $builder, $state)
    {
        if($state !== ''){
            $state = ReportState::from($state);
            $builder->andWhere("r.state = :state ")->setParameter(':state',$state);
        }
    }

    private function addType(QueryBuilder $builder, $type)
    {
        if($type !== ''){
            $type = ReportType::from($type);
            $builder->andWhere("r.type = :type ")->setParameter(':type',$type);
        }
    }

    private function addInterruption(QueryBuilder $builder, $interruption)
    {
        if($interruption !== ''){
            $interruption = InterruptionReason::from($interruption);
            $builder->andWhere("r.interruptionReason = :interruption ")->setParameter(':interruption',$interruption);
        }
    }

    private function findReportByConditions($state, $priority, $type, $filter, $interruption): QueryBuilder
    {
        $builder = $this->createQueryBuilder('r')->select(['r', 'e', 'mun', 'pro'])
            ->innerJoin('r.equipment', 'e')
            ->leftJoin('e.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
        $this->addState($builder, $state);
        $this->addPriority($builder, $priority);
        $this->addType($builder, $type);
        $this->addInterruption($builder, $interruption);
        $this->addFilter($builder, $filter);

        return $builder;
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @param string $priority
     * @param string $state
     * @return Paginator Returns an array of User objects
     */
    public function findReports(string $filter = '', int $amountPerPage = 10, int $page = 1, $priority = '', $state = '', $type = '', $interruption = ''): Paginator
    {
        $builder = $this->findReportByConditions($state, $priority, $type, $filter, $interruption);
        $query = $builder->orderBy('r.priority', 'DESC')
                         ->addOrderBy('r.state', 'DESC')
                         ->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findOpenAmountReports($type): int
    {
        $builder = $this->createQueryBuilder('r')->select('COUNT(r) as total');
        $this->addState($builder, ReportState::Open->value);
        $this->addPriority($builder, '');
        $this->addType($builder, $type->value);
        $this->addFilter($builder, '');
        return $builder->getQuery()->getSingleScalarResult();
    }

    public function findInterruptionAndEquipment($interruption, $type=null)
    {
        $builder = $this->createQueryBuilder('r')->select('COUNT(r) as total');
        $this->addState($builder, ReportState::Open->value);
//        $this->addPriority($builder, '');
        if(!is_null($type)){
            $this->addType($builder, $type->value);
        }
        $this->addFilter($builder, (new \DateTime('now'))->format('d/m/Y'));
        $builder->andWhere("r.interruptionReason = :reason ")
                ->setParameter(':reason',$interruption);
        return $builder->getQuery()->getSingleScalarResult();
    }

    public function findResolvedInterruption()
    {
        $builder = $this->createQueryBuilder('r')->select('COUNT(r) as total');
        $this->addState($builder, ReportState::Close->value);
        $builder->andWhere("r.closeDate LIKE :filter ")
                ->setParameter(':filter',(new \DateTime('now'))->format('Y-m-d').'%');
        return $builder->getQuery()->getSingleScalarResult();
    }

    public function findOpenForEquipment(Equipment $equipment)
    {
        $builder = $this->createQueryBuilder('r')->select('COUNT(r) as total');
        $this->addState($builder, ReportState::Open->value);
//        $this->addPriority($builder, '');
//        $this->addType($builder, $type->value);
        $builder->andWhere("r.equipment = :equipment")->setParameter(':equipment',$equipment);
        return $builder->getQuery()->getSingleScalarResult();
    }

    public function findOpenAmountReportsByMonth($month): int
    {
        $builder = $this->createQueryBuilder('r')->select('COUNT(r) as total');
        $builder->andWhere("r.entryDate LIKE :filter ")->setParameter(':filter','%'.(new \DateTime())->format('Y').'-'.$month.'-%');
        return $builder->getQuery()->getSingleScalarResult();
    }

//    /**
//     * @return Report[] Returns an array of Report objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Report
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

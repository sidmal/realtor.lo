<?php

namespace Application\Sonata\UserBundle\Entity\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\Query\Parameter;

/**
 * DutyInBranchRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DutyInBranchRepository extends EntityRepository
{
    protected static $monthRus = [
        1 => 'январь',
        2 => 'февраль',
        3 => 'март',
        4 => 'апрель',
        5 => 'май',
        6 => 'июнь',
        7 => 'июль',
        8 => 'август',
        9 => 'сентябрь',
        10 => 'октябрь',
        11 => 'ноябрь',
        12 => 'декабрь'
    ];

    public function getMonthRus($monthNumber)
    {
        return self::$monthRus[$monthNumber];
    }

    public function getDutyCountByDateAndBranch(\DateTime $date, $branch = null)
    {
        $builder = $this->getEntityManager()->createQueryBuilder()
            ->from('ApplicationSonataUserBundle:DutyInBranches', 'duty_in_branch')
            ->where('duty_in_branch.dutyDate = :duty_date')
            ->setParameter('duty_date', $date, Type::DATE)
            ->setMaxResults(1)
        ;

        $builder->select($builder->expr()->count('duty_in_branch.id'));

        if($branch){
            $builder->andWhere('duty_in_branch.branchId = :branch_id')
                ->setParameter('branch_id', $branch);
        }

        try{
            $result = $builder->getQuery()->getSingleScalarResult();
        }
        catch(NoResultException $e){
            $result = null;
        }

        return $result;
    }

    public function getDutyInBranchByDate($dutyDate, $branch = null, $manager = null)
    {
        $builder = $this->getEntityManager()->createQueryBuilder()
            ->select(
                [
                    'duty.id AS duty_id',
                    'duty.dutyDate',
                    'duty.dutyTime',
                    'duty.dutyPhone',
                    'duty_agent.id AS duty_agent_id',
                    'duty_agent.fio',
                    'duty_branch.id AS branch_id',
                    'duty_branch.name',
                    'duty_head.id AS manager_id'
                ]
            )
            ->from('ApplicationSonataUserBundle:DutyInBranches', 'duty')
            ->leftJoin('duty.dutyAgent', 'duty_agent')
            ->leftJoin('duty.branchId', 'duty_branch')
            ->leftJoin('duty_agent.head', 'duty_head')
            ->where('duty.dutyDate = :duty_date')->setParameter('duty_date', $dutyDate, Type::DATE)
            ->orderBy(new OrderBy('duty.branchId'))
            ->addOrderBy(new OrderBy('duty.dutyDate'))
            ->addOrderBy(new OrderBy('duty.dutyTime'));

        if($branch){
            $builder->andWhere('duty.branchId = :branch_id')->setParameter('branch_id', $branch);
        }

        if($manager){
            $builder->andWhere('duty_head.id = :manager')->setParameter('manager', $manager);
        }

        try{
            $queryResult = $builder->getQuery()->getArrayResult();

            $result = [];
            foreach($queryResult as $item){
                $dutyTimeStart = sprintf('%02d', $item['dutyTime']->format('H'));
                $dutyTimeEnd = sprintf('%02d', ($item['dutyTime']->format('H') + 1));

                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['duty_id'][] = $item['duty_id'];
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['manager_id'][] = $item['manager_id'];
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['branch_id'][] = $item['branch_id'];
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['duty_day'][] = $item['dutyDate']->format('d');
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['duty_month'][] = $item['dutyDate']->format('m');
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['duty_year'][] = $item['dutyDate']->format('Y');
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['duty_hour'][] = $item['dutyTime']->format('H');
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['duty_agent_id'][] = $item['duty_agent_id'];
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['agent_name'][] = $item['fio'];
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['phone'][] = $item['dutyPhone'];

                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['duty_time_start'][] = $dutyTimeStart;
                $result[$item['name']][$dutyTimeStart][$dutyTimeEnd]['duty_time_end'][] = $dutyTimeEnd;
            }
        }
        catch(NoResultException $e){
            $result = null;
        }

        return $result;
    }

    public function getDuty($duty_period, $manager = null)
    {
        $builder = $this->getEntityManager()->createQueryBuilder()
            ->select(
                [
                    'duty.id AS duty_id',
                    'duty.dutyDate',
                    'duty.dutyTime',
                    'duty.dutyPhone',
                    'duty_agent.id AS duty_agent_id',
                    'duty_agent.fio',
                    'duty_branch.id AS branch_id',
                    'duty_branch.name',
                    'duty_head.id AS manager_id',
                    'duty_head.fio AS manager_fio'
                ]
            )
            ->from('ApplicationSonataUserBundle:DutyInBranches', 'duty')
            ->leftJoin('duty.dutyAgent', 'duty_agent')
            ->leftJoin('duty.branchId', 'duty_branch')
            ->leftJoin('duty_agent.head', 'duty_head')
            ->addOrderBy(new OrderBy('duty.dutyDate', 'asc'))
            ->addOrderBy(new OrderBy('duty.dutyTime', 'asc'))
            ->addOrderBy(new OrderBy('duty_branch.id', 'asc'));

        $duty_period_start = new \DateTime($duty_period->format('Y-m-d'));
        $duty_period_end = $duty_period->modify('last day of 0 month');

            $builder->where($builder->expr()->between('duty.dutyDate', ':duty_date_start', ':duty_date_end'));
        $builder->setParameters(
            new ArrayCollection(
                [
                    new Parameter('duty_date_start', $duty_period_start, Type::DATETIME),
                    new Parameter('duty_date_end', $duty_period_end, Type::DATETIME)
                ]
            )
        );

        if($manager){
            $builder->andWhere('duty_head.id = :manager')->setParameter('manager', $manager);
        }

        try{
            $duty_result = $builder->getQuery()->getArrayResult();

            $result = [];
            foreach($duty_result as $item){
                $result[$item['name']][$item['manager_fio']][$item['dutyDate']->format('d.m.Y')]
                    [$item['dutyTime']->format('H')]['agents'][] = $item['fio'];
            }
        }
        catch(NoResultException $e){
            $result = null;
        }

        return $result;
    }

    public function getDutyCountByDate($duty_date, $branch = null)
    {
        $builder = $this->getEntityManager()->createQueryBuilder()
            ->from('ApplicationSonataUserBundle:DutyInBranches', 'duty')
            ->where('duty.dutyDate = :duty_date')->setParameter('duty_date', $duty_date, Type::DATETIME);

        $builder->select($builder->expr()->count('duty.id'));

        if($branch){
            $builder->andWhere('duty.branchId = :branch_id')->setParameter('branch_id', $branch);
        }

        try{
            $result = $builder->getQuery()->getSingleScalarResult();
        }
        catch(NoResultException $e){
            $result = null;
        }

        return $result;
    }
}

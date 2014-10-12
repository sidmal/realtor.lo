<?php

namespace Realtor\CallBundle\Entity\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * AtsCallsDataRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AtsCallsDataRepository extends EntityRepository
{
    public function getAtsCallsData($report_fields, $limit, $offset, $sort_field, $sort_direction, $conditions, $global_search)
    {
        $select = []; $numeric_fields = []; $select_condition = [];
        foreach($report_fields as $field){
            $select[] = $field["query_field"];

            if($field["filter_type"] == "number"){
                $numeric_fields[] = $field["query_field"];
            }
        }

        $builder = $this->getEntityManager()->createQueryBuilder()
            ->select($select)
            ->from('CallBundle:AtsCallData', 'ats_calls_data')
            ->leftJoin('ats_calls_data.callByApplication', 'call_by_application')
            ->leftJoin('call_by_application.params', 'call_params')
            ->leftJoin('call_params.message', 'call_message')
            ->leftJoin('call_params.advertisingSource', 'advertising_source')
            ->leftJoin('call_params.other_who_call', 'other_who_call')
            ->leftJoin('call_params.reason', 'reason')
            ->leftJoin('call_by_application.callResult', 'call_result')
            ->leftJoin('call_params.branch', 'branch')
            ->setMaxResults($limit)->setFirstResult($offset)
            ->orderBy($sort_field, $sort_direction)
        ;

        if(!empty($conditions)){
            foreach($conditions as $key => $condition_item){
                if($condition_item["field"] == "ats_calls_data.callDate"){
                    list($start_date, $end_date) = explode("~", $condition_item['value']);

                    try{
                        $start_date = new \DateTime($start_date);
                        $end_date = new \DateTime($end_date);
                    }
                    catch(\Exception $e){
                        continue;
                    }

                    $builder->andWhere($builder->expr()->between($condition_item['field'], ':start_date', ':end_date'))
                        ->setParameter('start_date', $start_date, Type::DATE)
                        ->setParameter('end_date', $end_date, Type::DATE);
                }
                elseif($condition_item["field"] == "ats_calls_data.callTime"){
                    list($start_time, $end_time) = explode("~", $condition_item['value']);

                    try{
                        $start_time = new \DateTime($start_date);
                        $end_time = new \DateTime($end_date);
                    }
                    catch(\Exception $e){
                        continue;
                    }

                    $builder->andWhere($builder->expr()->between($condition_item['field'], ':start_date', ':end_date'))
                        ->setParameter('start_date', $start_time, Type::TIME)
                        ->setParameter('end_date', $end_time, Type::TIME);
                }
                elseif(in_array($condition_item["field"], $numeric_fields)){
                    $builder->andWhere($condition_item['field'].' = :param')
                        ->setParameter('param', $condition_item['value']);
                }
                else{
                    $builder->andWhere($builder->expr()->like($builder->expr()->lower($condition_item['field']), $builder->expr()->lower(':condition_value')))
                        ->setParameter('condition_value', '%' . $condition_item['value'] . '%');
                }
            }
        }

        if(!empty($global_search)){
            foreach($report_fields as $item){
                $field_name = (strpos($item["query_field"], " AS")) ?
                    substr($item["query_field"], 0, strpos($item["query_field"], " AS")) : $item["query_field"];

                if($item["filter_type"] == "number"){
                    $builder->orWhere($field_name.' = :param')
                        ->setParameter('param', $global_search);
                }
                elseif($item["filter_type"] == "number-range"){
                    try{
                        $global_search = new \DateTime($global_search);
                    }
                    catch(\Exception $e){
                        continue;
                    }

                    $builder->orWhere($field_name.' = :param')
                        ->setParameter('param', $global_search, Type::DATETIME);
                }
                else{
                    $builder->orWhere($builder->expr()->like($builder->expr()->lower($field_name), $builder->expr()->lower(':param')))
                        ->setParameter('param', $global_search);
                }
            }
        }

        try{
            $result = $builder->getQuery()->getArrayResult();

            foreach($result as $key => $value){
                if(!isset($value["message"])){
                    $result[$key]["message"] = null;
                }
            }
        }
        catch(NoResultException $e){
            $result = null;
        }

        return $result;
    }

    public function getCountAtsCallsData()
    {
        $builder = $this->getEntityManager()->createQueryBuilder()
            ->from('CallBundle:AtsCallData', 'ats_calls_data')
        ;

        $builder->select($builder->expr()->count('ats_calls_data.id'));

        try{
            $result = $builder->getQuery()->getSingleScalarResult();
        }
        catch(NoResultException $e){
            $result = null;
        }

        return $result;
    }
}

<?php

namespace Realtor\CallBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Parameter;

/**
 * CallRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CallRepository extends EntityRepository
{
    public function getIncomeCall($forPhone, array $events)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(
                [
                    'call.id',
                    'call.linkedId',
                    'call.fromPhone',
                    'call.callAction',
                    'call.atsCallId',
                    'call_params.callerName',
                    'IDENTITY(call_params.advertisingSource) advertisingSource',
                    'call_params.propertyId',
                    'call_params.propertyAddress',
                    'IDENTITY(call_params.propertyAgentId) propertyAgentId',
                    'call_params.propertyBaseId',
                    'IDENTITY(call_params.reason) reason',
                    'call_params.message',
                    'call_params.callType',
                ]
            )
            ->from('CallBundle:Call', 'call')
            ->leftJoin('call.params', 'call_params')
            ->where('call.type = 1')
            ->andWhere('call.toPhone = :forPhone')
            ->setMaxResults(1)
            ->orderBy('call.eventAt', 'desc');

        $qb->andWhere($qb->expr()->in('call.callAction', ':events'));
        $qb->andWhere($qb->expr()->between('call.createdAt', ':dateFrom', ':dateTo'));

        $qb->setParameters(
            new ArrayCollection(
                [
                    new Parameter('forPhone', $forPhone),
                    new Parameter('events', $events),
                    new Parameter('dateFrom', (new \DateTime())->sub(new \DateInterval('PT8S')), Type::DATETIME),
                    new Parameter('dateTo', (new \DateTime())->add(new \DateInterval('PT8S')), Type::DATETIME),
                ]
            )
        );

        try{
            $result = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        }
        catch(NoResultException $e){
            $result = null;
        }

        return $result;
    }
}

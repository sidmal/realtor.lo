<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 26.06.14
 * Time: 21:54
 */

namespace Application\Sonata\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class DutyController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/duty/get/unfilled", name="duty_get_unfilled")
     * @Method({"GET"})
     */
    public function getUnfilledDutyAction(Request $request)
    {
        $response = new JsonResponse();

        if(!$request->isXmlHttpRequest()){
            return $response->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        if(!$request->query->has('year') || !$request->query->has('month')){
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $dutyStartDate = new \DateTime($request->query->get('year').'-'.sprintf('%02d', $request->query->get('month')).'-01');
        $dutyEndDate = new \DateTime();

        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($dutyStartDate, $interval ,$dutyEndDate);

        $dutyTime = (integer)$this->container->getParameter('duty.max.hour') -
            (integer)$this->container->getParameter('duty.min.hour');

        $branches = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')->findBy(['isActive' => true]);

        if(!$branches){
            return $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $dutyAgentsCountTotal = 0;
        foreach($branches as $branch){
            $dutyAgentsCountTotal += $branch->getDutyAgentsCount();
        }

        $calendarDate = [];

        foreach($dateRange as $dateItem){
            $item = ['body' => '', 'date' => $dateItem->format('Y-m-d')];

            $dutyCountByDate = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:DutyInBranches')->getDutyCountByDateAndBranch($dateItem);
            $dutyRecordCount = $dutyTime * $dutyAgentsCountTotal;

            if($dutyCountByDate == 0){
                $item['classname'] = 'grade-1';
                $item['title'] = 'Филиалы (Нет заполненных дежурств)';
            }
            elseif($dutyCountByDate > 0 && $dutyCountByDate < $dutyRecordCount){
                $item['classname'] = 'grade-2';
                $item['title'] = 'Филиалы (Частично заполнен график дежурств)';
            }
            else{
                $item['classname'] = 'grade-4';
                $item['title'] = 'Филиалы';
            }

            foreach($branches as $branch){
                $dutyAsDate = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:DutyInBranches')->getDutyCountByDateAndBranch($dateItem, $branch);
                $dutyRecordCount = $dutyTime * $branch->getDutyAgentsCount();

                if($dutyAsDate == 0){
                    $item['body'] .= '<li style="color: #FA2601;">'.$branch->getName().' (количество не заполенных часов дежурства: '.($dutyRecordCount - $dutyAsDate).')</li>';
                }
                elseif($dutyAsDate > 0 && $dutyAsDate < $dutyRecordCount){
                    $item['body'] .= '<li style="color: #FA8A00">'.$branch->getName().' (количество не заполенных часов дежурства: '.($dutyRecordCount - $dutyAsDate).')</li>';
                }
                else{
                    $item['body'] .= '<li style="color: #27AB00">'.$branch->getName().'</li>';
                }

            }

            $calendarDate[] = $item;
        }

        return $response->setData($calendarDate);
    }
} 
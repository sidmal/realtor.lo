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
        $dutyEndDate = (new \DateTime($request->query->get('year').'-'.sprintf('%02d', $request->query->get('month')).'-01'))
            ->modify('last day of 0 month')->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($dutyStartDate, $interval ,$dutyEndDate);

        $dutyStartTime = (integer)$this->container->getParameter('duty.min.hour');
        $dutyEndTime = (integer)$this->container->getParameter('duty.max.hour');
        $dutyTime = $dutyStartTime - $dutyEndTime;

        $totalDutyAgents = null;
        if($request->query->has('branch')){
            $totalDutyAgents = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
                ->getTotalDutyAgents($request->query->get('branch'));
        }
        else{
            $totalDutyAgents = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
                ->getTotalDutyAgents();
        }

        if(!$totalDutyAgents){
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $calendarDate = [];

        foreach($dateRange as $dateItem){
            $item = [
                'body' => '',
                'title' => '<strong>График дежурств на '.$dateItem->format('d.m.Y').'</strong>',
                'date' => $dateItem->format('Y-m-d')
            ];

            $dutyData = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:DutyInBranches')
                ->getDutyInBranchByDate($dateItem, $request->query->has('branch') ? $request->query->get('branch') : null);

            if(!$dutyData){
                $branches = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
                    ->findBy(['isActive' => true]);

                foreach($branches as $branch){
                    $item['body'] .= '<div class="span6" style="margin-bottom: 10px;">'.$branch->getName();

                    $item['body'] .= '<table class="table table-bordered" style="width: 100%;">';
                    $item['body'] .= '<tr>';
                    $item['body'] .= '<th style="width: 10%; text-align: center; vertical-align: middle;">Время начала дежурства</th>';
                    $item['body'] .= '<th style="width: 10%; text-align: center; vertical-align: middle;">Время окончания дежурства</th>';
                    $item['body'] .= '<th style="text-align: center; vertical-align: middle;">Дежурный</th>';
                    $item['body'] .= '</tr>';
                    for($i = $dutyStartTime; $i < $dutyEndTime; $i++){
                        $item['body'] .= '<tr>';
                        $item['body'] .= '<td style="width: 10%; text-align: center; vertical-align: middle;">'.sprintf('%02d', $i).'</td>';
                        $item['body'] .= '<td style="width: 10%; text-align: center; vertical-align: middle;">'.sprintf('%02d', ($i + 1)).'</td>';
                        $item['body'] .= '<td style="text-align: center; vertical-align: middle;">&nbsp;</td>';
                        $item['body'] .= '</tr>';
                    }
                    $item['body'] .= '</table>';

                    $item['body'] .= '</div>';
                }
            }

            $dutyRecordCount = $dutyTime * $totalDutyAgents;
            $dutyFilledOn = count(array_values($dutyData)) * count($dutyData) / $dutyRecordCount;

            if($dutyFilledOn <= 0.59){
                $item['classname'] = 'grade-1';
            }
            elseif($dutyFilledOn >= 0.60 && $dutyFilledOn < 1){
                $item['classname'] = 'grade-3';
            }
            else{
                $item['classname'] = 'grade-4';
            }

            $calendarDate[] = $item;
        }

        return $response->setData($calendarDate);
    }
} 
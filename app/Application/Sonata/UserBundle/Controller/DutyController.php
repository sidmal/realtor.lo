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
        $branches = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
            ->findBy(['isActive' => true]);

        foreach($dateRange as $dateItem){
            $item = [
                'body' => '',
                'title' => '<strong>График дежурств на '.$dateItem->format('d.m.Y').'</strong>',
                'date' => $dateItem->format('Y-m-d')
            ];

            $dutyData = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:DutyInBranches')
                ->getDutyInBranchByDate($dateItem, $request->query->has('branch') ? $request->query->get('branch') : null);

            foreach($branches as $branch){
                $item['body'] .= '<div class="span6">';
                $item['body'] .= '<div class="span6" style="margin-bottom: 15px; text-align: center;">';
                $item['body'] .= '<span class="label label-info" style="font-size: 18px;">'.$branch->getName().'</span>';
                $item['body'] .= '</div>';

                $item['body'] .= '<table class="table table-bordered" style="width: 100%;">';
                $item['body'] .= '<tr>';
                $item['body'] .= '<th style="width: 10%; text-align: center; vertical-align: middle;">Время начала дежурства</th>';
                $item['body'] .= '<th style="width: 10%; text-align: center; vertical-align: middle;">Время окончания дежурства</th>';
                $item['body'] .= '<th style="text-align: center; vertical-align: middle;">Дежурный</th>';
                $item['body'] .= '<th style="width: 10%; text-align: center; vertical-align: middle;"></th>';
                $item['body'] .= '</tr>';
                for($i = $dutyStartTime; $i < $dutyEndTime; $i++){
                    $item['body'] .= '<tr>';
                    $item['body'] .= '<td style="width: 10%; text-align: center; vertical-align: middle;">'.sprintf('%02d', $i).'</td>';
                    $item['body'] .= '<td style="width: 10%; text-align: center; vertical-align: middle;">'.sprintf('%02d', ($i + 1)).'</td>';

                    if(!$dutyData){
                        $item['body'] .= '<td style="text-align: center; vertical-align: middle;">&nbsp;</td>';
                        $item['body'] .= '<td style="text-align: center; vertical-align: middle;">&nbsp;</td>';
                    }
                    else{
                        if(isset($dutyData[$branch->getName()][sprintf('%02d', $i)][sprintf('%02d', ($i + 1))])){
                            $dutyItem = $dutyData[$branch->getName()][sprintf('%02d', $i)][sprintf('%02d', ($i + 1))];

                            $item['body'] .= '<td style="text-align: center; vertical-align: middle;">'.implode(', ', $dutyItem['agent_name']).'</td>';

                            if(count($dutyItem['agent_name']) > 1){
                                $agents = $dutyItem['agent_name'];

                                $item['body'] .= '<td style="width: 10%; text-align: center; vertical-align: middle;">';

                                $item['body'] .= '<div class="btn-group">';
                                $item['body'] .= '<button class="btn btn-mini">Редактировать запись</button>';
                                $item['body'] .= '<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">';
                                $item['body'] .= '<span class="caret"></span>';
                                $item['body'] .= '</button>';
                                $item['body'] .= '<ul class="dropdown-menu">';

                                foreach($agents as $agent){
                                    $item['body'] .= '<li>';
                                    $item['body'] .= '<a onclick="edit_duty_record(\''.$dutyItem['duty_id'][0].'\', \''.$dutyItem['branch_id'][0].'\', \''.$dutyItem['manager_id'][0].'\', \''.$dutyItem['duty_agent_id'][0].'\', \''.$dutyItem['phone'][0].'\', \''.$dutyItem['duty_day'][0].'\', \''.$dutyItem['duty_month'][0].'\', \''.$dutyItem['duty_year'][0].'\', \''.$dutyItem['duty_time_start'][0].'\', \''.$dutyItem['duty_time_end'][0].'\')">';
                                    $item['body'] .= '<i class="icon-edit"></i> '.$agent;
                                    $item['body'] .= '</a>';
                                    $item['body'] .= '</li>';
                                }

                                $item['body'] .= '</ul>';
                                $item['body'] .= '</div>';
                                $item['body'] .= '</td>';
                            }
                            else{
                                $item['body'] .= '<td style="width: 10%; text-align: center; vertical-align: middle;">';
                                $item['body'] .= '<a class="btn btn-small sonata-action-element" onclick="edit_duty_record(\''.$dutyItem['duty_id'][0].'\', \''.$dutyItem['branch_id'][0].'\', \''.$dutyItem['manager_id'][0].'\', \''.$dutyItem['duty_agent_id'][0].'\', \''.$dutyItem['phone'][0].'\', \''.$dutyItem['duty_day'][0].'\', \''.$dutyItem['duty_month'][0].'\', \''.$dutyItem['duty_year'][0].'\', \''.$dutyItem['duty_time_start'][0].'\', \''.$dutyItem['duty_time_end'][0].'\')">';
                                $item['body'] .= '<nobr><i class="icon-edit"></i> Редактировать</nobr>';
                                $item['body'] .= '</a>';
                                $item['body'] .= '</td>';
                            }
                        }
                        else{
                            $item['body'] .= '<td style="text-align: center; vertical-align: middle;">&nbsp;</td>';
                            $item['body'] .= '<td style="text-align: center; vertical-align: middle;">&nbsp;</td>';
                        }
                    }

                    $item['body'] .= '</tr>';
                }
                $item['body'] .= '</table>';

                $item['body'] .= '</div>';
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
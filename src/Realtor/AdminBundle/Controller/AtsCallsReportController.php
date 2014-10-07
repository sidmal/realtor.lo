<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 02.10.14
 * Time: 23:33
 */

namespace Realtor\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AtsCallsReportController extends CRUDController
{
    public function listAction()
    {
        return parent::listAction();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     *
     * @Route("/get_ats_calls_report_data", name="get_ats_calls_report_data")
     */
    public function getAtsCallsReportDataAction(Request $request)
    {
        print_r($this->container->getParameter('report_fields')); exit;

        $offset = $request->query->get('iDisplayStart');
        $limit = $request->query->get('iDisplayLength');

        $response = new JsonResponse();

        if(!$this->get('request')->isXmlHttpRequest()){
            return $response->setData(
                [
                    'error' => 'Получен не корректный запрос.',
                    'draw' => 1,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ]
            )->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $reportData = $this->getDoctrine()->getManager()->getRepository('CallBundle:AtsCallData')
            ->getAtsCallsData($limit, $offset);

        if(!$reportData){
            return $response->setData(
                [
                    'error' => 'Данных не найдено.',
                    'draw' => 1,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ]
            )->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $resultData = [];
        foreach($reportData as $item){
            $resultData[] = [
                $item['nrec'],
                $item['callDate']->format('Y-m-d'),
                $item['callTime']->format('H:i:s'),
                $item['clid'],
                $item['src'],
                $item['dst'],
                $item['dcontext'],
                $item['channel'],
                $item['dstchannel'],
                $item['lastapp'],
                $item['lastdata'],
                $item['duration'],
                $item['billsec'],
                $item['disposition'],
                $item['amaflags'],
                $item['accountcode'],
                $item['uniqueid'],
                $item['userfield'],
                $item['xTag'],
                $item['xCid'],
                $item['xDid'],
                $item['xDialed'],
                $item['xSpec'],
                $item['xInsecure'],
                $item['xResult'],
                $item['xRecord'],
                $item['xDomain'],
                $item['linkedid'],
            ];
        }

        return $response->setData(
            [
                'sEcho' => $request->query->get('sEcho'),
                'iTotalRecords' => $this->getDoctrine()->getManager()->getRepository('CallBundle:AtsCallData')
                    ->getCountAtsCallsData(),
                'iTotalDisplayRecords' => $this->getDoctrine()->getManager()->getRepository('CallBundle:AtsCallData')
                    ->getCountAtsCallsData(),
                'aaData' => $resultData
            ]
        );
    }
} 
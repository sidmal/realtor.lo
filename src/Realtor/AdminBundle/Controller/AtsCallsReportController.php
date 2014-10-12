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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AtsCallsReportController extends CRUDController
{
    public function listAction()
    {
        if(false === $this->admin->isGranted('LIST')){
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), array(
            'action' => 'list',
            'form' => $formView,
            'datagrid' => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'fields_list' => $this->container->getParameter('report_fields')
        ));
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
        $report_fields = $this->container->getParameter('report_fields');

        $offset = $request->query->get('iDisplayStart');
        $limit = $request->query->get('iDisplayLength');

        $sort_field_number = $request->query->get('iSortCol_0');
        $sort_direction = $request->query->get('sSortDir_0');

        $global_search = $request->query->get('sSearch');

        $conditions = [];

        $request_params = $request->query->all();
        foreach($request_params as $param_name => $param_value){
            if(preg_match('/^sSearch_(\d{1})$/', $param_name, $matches)){
                if(empty($param_value) || $param_value == '~'){
                    continue;
                }

                $conditions[] = [
                    'field' => $report_fields[array_keys($report_fields)[$matches[1]]]['query_field'],
                    'value' => $param_value
                ];
            }
        }

        $sort_field = $report_fields[array_keys($report_fields)[$sort_field_number]]['query_field'];

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
            ->getAtsCallsData($report_fields, $limit, $offset, $sort_field, $sort_direction, $conditions, $global_search);

        if(!$reportData){
            return $response->setData(
                [
                    'sEcho' => 1,
                    'iTotalRecords' => 0,
                    'iTotalDisplayRecords' => 0,
                    'aaData' => []
                ]
            );
        }

        $resultData = [];
        foreach($reportData as $item){
            $result_item = [];

            foreach($item as $key => $value){
                if(array_key_exists($key, $report_fields)){
                    if($key == 'callDate'){
                        $result_item[] = $value->format('d.m.Y');
                    }
                    elseif($key == 'callTime'){
                        $result_item[] = $value->format('H:i:s');
                    }
                    else {
                        $result_item[] = $value;
                    }
                }
            }

            if(!empty($result_item)){
                $resultData[] = $result_item;
            }
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
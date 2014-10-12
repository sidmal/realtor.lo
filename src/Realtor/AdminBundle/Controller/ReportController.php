<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 21.07.14
 * Time: 1:44
 */

namespace Realtor\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

class ReportController extends CRUDController
{
    protected static $list_table_headers_name = [
        'event_at' => 'Дата звонка',
        'type' => 'Тип звонка (входящий/исходящий)',
        'fromPhone' => 'От кого звонок',
        'toPhone' => 'Кому звонок',
        'callType' => 'Тип обращения',
        'callerName' => 'Имя звонящего',
        'advertising_source_name' => 'Источник рекламы',
        'reason_name' => 'Причина обращения',
        'call_result_name' => 'Результат разговора',
        'callers_name' => 'Тип вызывающего',
    ];

    protected static function get_list_table_header_name($field_key)
    {
        return array_key_exists($field_key, self::$list_table_headers_name)
            ? self::$list_table_headers_name[$field_key] : $field_key;
    }

    public function listAction()
    {
        $request = $this->container->get('request');

        $params = $request->request->has('ReportFilter') ? $request->request->get('ReportFilter') : null;

        if(isset($params['day_start']) && isset($params['day_end'])){
            $date_start = $params['year_start'].'-'.$params['month_start'].'-'.$params['day_start'].' '.$params['hour_start'].':'.$params['minute_start'].':00';
            $date_end = $params['year_end'].'-'.$params['month_end'].'-'.$params['day_end'].' '.$params['hour_end'].':'.$params['minute_end'].':00';

            $conditions[0] = '$builder->andWhere($builder->expr()->between(\'call.eventAt\', \':date_start\', \':date_end\'))
                ->setParameter(\'date_start\', new \DateTime(\''.$date_start.'\'), \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter(\'date_end\', new \DateTime(\''.$date_end.'\'), \Doctrine\DBAL\Types\Type::DATETIME);';
        }

        if($params && !empty($params['fields'])){
            $report_data = $this->getDoctrine()->getManager()->getRepository('CallBundle:Call')
                ->getReport(
                    $params['fields'],
                    isset($conditions) ? $conditions : [],
                    isset($params['group_by']) ? $params['group_by'] : []
                );
        }

        if(!$this->admin->isGranted('LIST')){
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        if(isset($report_data)){
            $bar_chart_categories = [];

            $total_categories = []; $categories_by_date = []; $all_categories = [];
            foreach($report_data as $key => $item){
                foreach($item as $key_a => $item_a){
                    list($categorie, $count) = array_values($item_a);

                    $total_categories[$categorie] = (!isset($total_categories[$categorie])) ? 0 : $total_categories[$categorie] + $count;
                    $categories_by_date[$key][$categorie] = $count;
                    $all_categories[] = $categorie;
                }
            }

            foreach($all_categories as $item){
                $bar_chart_categories[$item] = [];

                foreach($report_data as $key => $item_a){
                    if(isset($categories_by_date[$key][$item])){
                        $bar_chart_categories[$item][] = $categories_by_date[$key][$item];
                    }
                    else{
                        $bar_chart_categories[$item][] = 0;
                    }
                }
            }
        }

        $render_params = [
            'action'     => 'list',
            'form'       => $formView,
            'report_data' => isset($report_data) ? $report_data : null,
            'previous_fields' => !empty($params['fields']) ? $params['fields'] : null,
            'bar_chart_categories' => isset($bar_chart_categories) ? $bar_chart_categories : null,
            'previous_group_by' => !empty($params['group_by']) ? $params['group_by'] : null,
            'previous_date_start' => isset($date_start) ? $date_start : null,
            'previous_date_end' => isset($date_end) ? $date_end : null,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ];

        if($render_params['report_data']){
            $render_params['table_header_fields'] = $params['fields'];

            foreach($render_params['table_header_fields'] as $key => $value){
                $render_params['table_header_fields'][$key] = self::get_list_table_header_name($value);
            }
        }

        return $this->render($this->admin->getTemplate('list'), $render_params);
    }
} 
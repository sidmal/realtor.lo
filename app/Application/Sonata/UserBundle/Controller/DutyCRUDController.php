<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 23.05.14
 * Time: 22:27
 */

namespace Application\Sonata\UserBundle\Controller;

use Application\Sonata\UserBundle\Entity\DutyInBranches;
use Application\Sonata\UserBundle\Form\DutyInBranchesType;
use Realtor\AdminBundle\Traits\Security;
use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DutyCRUDController extends CRUDController
{
    /**
     * @Route("/print", name="print")
     */
    public function printAction()
    {
        $request = $this->container->get('request');

        $redirect_response = new RedirectResponse($this->generateUrl('admin_sonata_user_duty_list'));

        if(!$request->query->has('duty_period')){
            return $redirect_response;
        }

        $user = $this->getUser();
        if($user->isManager()){
            $duty = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:DutyInBranches')
                ->getDuty(new \DateTime($request->query->get('duty_period')), $user->getId());
        }
        elseif($user->isDirector() || $user->isAdministrator()){
            $duty = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:DutyInBranches')
                ->getDuty(new \DateTime($request->query->get('duty_period')));
        }
        else{
            return $redirect_response;
        }

        if(!$duty){
            return $redirect_response;
        }

        return $this->render(
            '@ApplicationSonataUser/CRUD/print.html.twig',
            [
                'duty' => $duty,
                'duty_month' => mb_strtoupper(
                        $this->getDoctrine()->getManager()
                            ->getRepository('ApplicationSonataUserBundle:DutyInBranches')
                            ->getMonthRus(
                                (new \DateTime($request->query->get('duty_period')))->format('n')
                            ),
                        'utf-8').' '.(new \DateTime($request->query->get('duty_period')))->format('Y'),
                'duty_min_hour' => $this->container->getParameter('duty.min.hour'),
                'duty_max_hour' => $this->container->getParameter('duty.max.hour')
            ]
        );
    }

    public function listAction()
    {
        if(false === $this->admin->isGranted('LIST')){
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();

        $user = $this->getUser();
        if($user->isManager()){
            $branches = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
                ->findBy(['isActive' => true, 'id' => $user->getBranch()]);
            $managers = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')
                ->getManagers($user->getId());

        }
        else{
            $branches = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
                ->findBy(['isActive' => true]);
            $managers = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')
                ->getManagers();
        }

        $render_params = [
            'branches'   => $branches,
            'managers'   => $managers,
            'agents'     => $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')
                    ->getAgents(),
            'duty_hour_start' => $this->container->getParameter('duty.min.hour'),
            'duty_hour_end' => $this->container->getParameter('duty.max.hour'),
            'duty_hour_delta' => $this->container->getParameter('duty.delta.hour'),
            'action'     => 'list',
            'datagrid'   => $datagrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch')
        ];

        $request = $this->container->get('request');

        if($request->query->has('DutyFilter')){
            $request_params = $request->query->get('DutyFilter');

            $ajax_calendar_data = [];
            if(isset($request_params['branch_id']) && (integer)$request_params['branch_id'] > 0){
                $ajax_calendar_data['branch_id'] = (integer)$request_params['branch_id'];
            }

            if(isset($request_params['manager_id']) && (integer)$request_params['manager_id'] > 0){
                $ajax_calendar_data['manager_id'] = (integer)$request_params['manager_id'];
            }

            $render_params['ajax_calendar_data'] = json_encode($ajax_calendar_data);
        }

        return $this->render(
            $this->admin->getTemplate('list'), $render_params
        );
    }

    public function createAction()
    {
        $request = $this->container->get('request');
        $response = new JsonResponse();

        if(!$this->isXmlHttpRequest()){
            return $response
                ->setData(
                    [
                        'message_description' => ["Ошибка. Доступ запрещен. (ajax)"]
                    ]
                )
                ->setStatusCode(JsonResponse::HTTP_BAD_REQUEST);
        }

        if($request->getMethod() != 'POST'){
            return $response
                ->setData(
                    [
                        'message_description' => ["Ошибка. Доступ запрещен. (post)"]
                    ]
                )
                ->setStatusCode(JsonResponse::HTTP_FORBIDDEN);
        }

        if(false === $this->admin->isGranted('CREATE')) {
            return $response
                ->setData(
                    [
                        'message_description' => ["Ошибка. У пользователя нет прав на выполнение указанного действия."]
                    ]
                )
                ->setStatusCode(JsonResponse::HTTP_FORBIDDEN);
        }

        $params = $request->request->all();

        $form_duty = new DutyInBranches();
        $form = $this->createForm(new DutyInBranchesType(), $form_duty);
        $form->submit(
            [
                'branchId' => $params['Duty']['branch_id'],
                'dutyAgent' => $params['Duty']['agent_id'],
                'dutyPhone' => $params['Duty']['phone'],
                'dutyDate' => new \DateTime($params['Duty']['year_start'].'-'.$params['Duty']['month_start'].'-'.$params['Duty']['day_start']),
                'dutyTime' => (new \DateTime())->setTime($params['Duty']['hour_start'], 0, 0)
            ]
        );

        $duty_start = new \DateTime($params['Duty']['year_start'].'-'.$params['Duty']['month_start'].'-'.$params['Duty']['day_start'].' '.$params['Duty']['hour_start'].':00:00');
        $duty_end = new \DateTime($params['Duty']['year_end'].'-'.$params['Duty']['month_end'].'-'.$params['Duty']['day_end'].' '.$params['Duty']['hour_end'].':00:00');
        $interval = new \DateInterval('PT1H');

        $duty_range = new \DatePeriod($duty_start, $interval ,$duty_end);

        $validator = $this->container->get('validator');

        foreach($duty_range as $duty_item){
            if(isset($params['Duty']['duty_id']) && (integer)$params['Duty']['duty_id'] > 0){
                $duty = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:DutyInBranches')
                    ->find($params['Duty']['duty_id']);
            }
            else{
                $duty = new DutyInBranches();
            }

            $duty
                ->setBranchId($form_duty->getBranchId())
                ->setDutyAgent($form_duty->getDutyAgent())
                ->setDutyPhone($form_duty->getDutyPhone())
                ->setDutyDate($duty_item)
                ->setDutyTime($duty_item);

            if(count($errors = $validator->validate($duty)) > 0){

                $error_messages = [];
                foreach($errors as $error){
                    $error_messages[] = $error->getMessage();
                }

                return $response
                    ->setData(['message_description' => $error_messages])
                    ->setStatusCode(JsonResponse::HTTP_BAD_REQUEST);
            }

            if(!isset($params['Duty']['duty_id']) || (integer)$params['Duty']['duty_id'] <= 0){
                $branch_agent_count = $form_duty->getBranchId()->getDutyAgentsCount();
                $current_duty_agent_in_branch = $this->getDoctrine()->getManager()
                    ->getRepository('ApplicationSonataUserBundle:DutyInBranches')
                    ->findBy(
                        [
                            'branchId' => $form_duty->getBranchId(),
                            'dutyDate' => $duty_item,
                            'dutyTime' => $duty_item
                        ]
                    );

                if($current_duty_agent_in_branch && count($current_duty_agent_in_branch) >= $branch_agent_count){
                    return $response
                        ->setData(
                            [
                                'message_description' => ["Для указанного филиала уже установлено максимальное количество дежурных на указанное время."]
                            ]
                        )
                        ->setStatusCode(JsonResponse::HTTP_BAD_REQUEST);
                }
            }

            $this->getDoctrine()->getManager()->persist($duty);
            $this->getDoctrine()->getManager()->flush();
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function deleteAction($id)
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        $request = $this->container->get('request');

        if($request->query->has('DutyFilter')){
            $duty_filter_params = $request->query->get('DutyFilter');
        }

        if(!$object){
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if(false === $this->admin->isGranted('DELETE', $object)){
            throw new AccessDeniedException();
        }

        if($this->getRestMethod() == 'DELETE'){
            $this->validateCsrfToken('sonata.delete');

            try{
                $this->admin->delete($object);

                if($this->isXmlHttpRequest()){
                    return $this->renderJson(array('result' => 'ok'));
                }

                $this->addFlash(
                    'sonata_flash_success',
                    $this->admin->trans('flash_delete_success', array('%name%' => $this->admin->toString($object)),
                        'SonataAdminBundle')
                );

            }
            catch(ModelManagerException $e){
                if($this->isXmlHttpRequest()){
                    return $this->renderJson(array('result' => 'error'));
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->admin->trans('flash_delete_error', array('%name%' => $this->admin->toString($object)),
                        'SonataAdminBundle')
                );
            }

            if($request->request->has('DutyFilter')){
                return new RedirectResponse(
                    $this->admin->generateUrl('list', ['DutyFilter' => $request->request->get('DutyFilter')])
                );
            }
            else{
                return new RedirectResponse($this->admin->generateUrl('list'));
            }
        }

        return $this->render(
            $this->admin->getTemplate('delete'),
            [
                'object' => $object,
                'action' => 'delete',
                'branch_id' => isset($duty_filter_params) && isset($duty_filter_params['branch_id']) ? $duty_filter_params['branch_id'] : null,
                'manager_id' => isset($duty_filter_params) && isset($duty_filter_params['manager_id']) ? $duty_filter_params['manager_id'] : null,
                'csrf_token' => $this->getCsrfToken('sonata.delete')
            ]
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/unfilled", name="duty_unfilled")
     */
    public function unfilledAction(Request $request)
    {
        $response = new JsonResponse();

        $delete_url_params = [];
        if($request->query->has('branch_id')){
            $delete_url_params['DutyFilter[branch_id]'] = $request->query->get('branch_id');
        }

        if($request->query->has('manager_id')){
            $delete_url_params['DutyFilter[manager_id]'] = $request->query->get('manager_id');
        }

        if(!$request->isXmlHttpRequest()){
            return $response->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        if(!$request->query->has('year') || !$request->query->has('month')){
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        if($user->isManager()){
            $manager_agents = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')
                ->getAgentByManager($user->getId());
        }

        $dutyStartDate = new \DateTime($request->query->get('year').'-'.sprintf('%02d', $request->query->get('month')).'-01');
        $dutyEndDate = (new \DateTime($request->query->get('year').'-'.sprintf('%02d', $request->query->get('month')).'-01'))
            ->modify('last day of 0 month')->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($dutyStartDate, $interval ,$dutyEndDate);

        $dutyStartTime = (integer)$this->container->getParameter('duty.min.hour');
        $dutyEndTime = (integer)$this->container->getParameter('duty.max.hour');
        $dutyTime = $dutyEndTime - $dutyStartTime;

        $totalDutyAgents = null;
        if($request->query->has('branch_id')){
            $totalDutyAgents = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
                ->getTotalDutyAgents($request->query->get('branch_id'));
        }
        else{
            $totalDutyAgents = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
                ->getTotalDutyAgents();
        }

        if(!$totalDutyAgents){
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $calendarDate = [];
        if($request->query->has('branch_id')){
            $branches = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
                ->findBy(['isActive' => true, 'id' => $request->query->get('branch_id')]);
        }
        else{
            $branches = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
                ->findBy(['isActive' => true]);
        }

        foreach($dateRange as $dateItem){
            $item = [
                'body' => '',
                'title' => '<strong>График дежурств на '.$dateItem->format('d.m.Y').'</strong>',
                'date' => $dateItem->format('Y-m-d')
            ];

            $dutyData = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:DutyInBranches')
                ->getDutyInBranchByDate(
                    $dateItem,
                    $request->query->has('branch_id') ? $request->query->get('branch_id') : null,
                    $request->query->has('manager_id') ? $request->query->get('manager_id') : null
                );

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
                        $item['body'] .= '<td style="text-align: center; vertical-align: middle;">';
                        $item['body'] .= '<div class="btn-group">';
                        $item['body'] .= '<a class="btn btn-small sonata-action-element" onclick="add_duty_record_by_time('.$branch->getId().', \''.$dateItem->format('d').'\', \''.$dateItem->format('m').'\', \''.$dateItem->format('Y').'\', \''.$i.'\')">';
                        $item['body'] .= '<i class="icon-plus"></i>';
                        $item['body'] .= '</a>';
                        $item['body'] .= '</td>';
                    }
                    else{
                        if(isset($dutyData[$branch->getName()][sprintf('%02d', $i)][sprintf('%02d', ($i + 1))])){
                            $dutyItem = $dutyData[$branch->getName()][sprintf('%02d', $i)][sprintf('%02d', ($i + 1))];

                            $item['body'] .= '<td style="text-align: center; vertical-align: middle;">'.implode(', ', $dutyItem['agent_name']).'</td>';

                            if(count($dutyItem['agent_name']) > 1){
                                $agents = $dutyItem['agent_name'];

                                $item['body'] .= '<td style="width: 10%; text-align: center; vertical-align: middle;">';

                                $agents_duty_edit = '';
                                foreach($agents as $agent){
                                    if(isset($manager_agents)){
                                        if(in_array($dutyItem['duty_agent_id'][0], array_values($manager_agents))){
                                            $agents_duty_edit .= '<li>';
                                            $agents_duty_edit .= '<a onclick="edit_duty_record(\''.$dutyItem['duty_id'][0].'\', \''.$dutyItem['branch_id'][0].'\', \''.$dutyItem['manager_id'][0].'\', \''.$dutyItem['duty_agent_id'][0].'\', \''.$dutyItem['phone'][0].'\', \''.$dutyItem['duty_day'][0].'\', \''.$dutyItem['duty_month'][0].'\', \''.$dutyItem['duty_year'][0].'\', \''.$dutyItem['duty_time_start'][0].'\', \''.$dutyItem['duty_time_end'][0].'\')">';
                                            $agents_duty_edit .= '<i class="icon-edit"></i> '.$agent;
                                            $agents_duty_edit .= '</a>';
                                            $agents_duty_edit .= '</li>';
                                        }
                                    }
                                    else{
                                        $agents_duty_edit .= '<li>';
                                        $agents_duty_edit .= '<a onclick="edit_duty_record(\''.$dutyItem['duty_id'][0].'\', \''.$dutyItem['branch_id'][0].'\', \''.$dutyItem['manager_id'][0].'\', \''.$dutyItem['duty_agent_id'][0].'\', \''.$dutyItem['phone'][0].'\', \''.$dutyItem['duty_day'][0].'\', \''.$dutyItem['duty_month'][0].'\', \''.$dutyItem['duty_year'][0].'\', \''.$dutyItem['duty_time_start'][0].'\', \''.$dutyItem['duty_time_end'][0].'\')">';
                                        $agents_duty_edit .= '<i class="icon-edit"></i> '.$agent;
                                        $agents_duty_edit .= '</a>';
                                        $agents_duty_edit .= '</li>';
                                    }
                                }

                                if(!empty($agents_duty_edit)){
                                    $item['body'] .= '<div class="btn-group">';
                                    $item['body'] .= '<button class="btn btn-mini">Редактировать запись</button>';
                                    $item['body'] .= '<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">';
                                    $item['body'] .= '<span class="caret"></span>';
                                    $item['body'] .= '</button>';
                                    $item['body'] .= '<ul class="dropdown-menu">';
                                    $item['body'] .= $agents_duty_edit;
                                    $item['body'] .= '</ul>';
                                    $item['body'] .= '</div>';
                                }

                                $agents_duty_remove = '';
                                foreach($agents as $agent){
                                    if(isset($manager_agents)){
                                        if(in_array($dutyItem['duty_agent_id'][0], array_values($manager_agents))){
                                            $agents_duty_remove .= '<li>';
                                            $agents_duty_remove .= '<a href="'.$this->generateUrl('admin_sonata_user_dutyinbranches_delete', array_merge(['id' => $dutyItem['duty_id'][0]], $delete_url_params)).'">';
                                            $agents_duty_remove .= '<i class="icon-remove"></i> '.$agent;
                                            $agents_duty_remove .= '</a>';
                                            $agents_duty_remove .= '</li>';
                                        }
                                    }
                                    else{
                                        $agents_duty_remove .= '<li>';
                                        $agents_duty_remove .= '<a href="'.$this->generateUrl('admin_sonata_user_dutyinbranches_delete', array_merge(['id' => $dutyItem['duty_id'][0]], $delete_url_params)).'">';
                                        $agents_duty_remove .= '<i class="icon-remove"></i> '.$agent;
                                        $agents_duty_remove .= '</a>';
                                        $agents_duty_remove .= '</li>';
                                    }
                                }

                                if(!empty($agents_duty_remove)){
                                    $item['body'] .= '<div class="btn-group">';
                                    $item['body'] .= '<button class="btn btn-mini">Удалить запись</button>';
                                    $item['body'] .= '<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">';
                                    $item['body'] .= '<span class="caret"></span>';
                                    $item['body'] .= '</button>';
                                    $item['body'] .= '<ul class="dropdown-menu">';
                                    $item['body'] .= $agents_duty_remove;
                                    $item['body'] .= '</ul>';
                                    $item['body'] .= '</div>';
                                }

                                if(empty($agents_duty_edit) && empty($agents_duty_remove)){
                                    $item['body'] .= '&nbsp;';
                                }

                                $item['body'] .= '</td>';
                            }
                            else{
                                $item['body'] .= '<td style="width: 10%; text-align: center; vertical-align: middle;">';

                                if(isset($manager_agents)){
                                    if(in_array($dutyItem['duty_agent_id'][0], array_values($manager_agents))){
                                        $item['body'] .= '<div class="btn-group">';
                                        $item['body'] .= '<a class="btn btn-small sonata-action-element" onclick="edit_duty_record(\''.$dutyItem['duty_id'][0].'\', \''.$dutyItem['branch_id'][0].'\', \''.$dutyItem['manager_id'][0].'\', \''.$dutyItem['duty_agent_id'][0].'\', \''.$dutyItem['phone'][0].'\', \''.$dutyItem['duty_day'][0].'\', \''.$dutyItem['duty_month'][0].'\', \''.$dutyItem['duty_year'][0].'\', \''.$dutyItem['duty_time_start'][0].'\', \''.$dutyItem['duty_time_end'][0].'\')">';
                                        $item['body'] .= '<i class="icon-edit"></i>';
                                        $item['body'] .= '</a>';

                                        $item['body'] .= '<a class="btn btn-small sonata-action-element" href="'.$this->generateUrl('admin_sonata_user_dutyinbranches_delete', array_merge(['id' => $dutyItem['duty_id'][0]], $delete_url_params)).'">';
                                        $item['body'] .= '<i class="icon-remove"></i>';
                                        $item['body'] .= '</a>';

                                        if($branch->getDutyAgentsCount() > 1){
                                            $item['body'] .= '<a class="btn btn-small sonata-action-element" onclick="add_duty_record_by_time('.$branch->getId().', \''.$dateItem->format('d').'\', \''.$dateItem->format('m').'\', \''.$dateItem->format('Y').'\', \''.$i.'\')">';
                                            $item['body'] .= '<i class="icon-plus"></i>';
                                            $item['body'] .= '</a>';
                                        }

                                        $item['body'] .= '</div>';
                                    }
                                    else{
                                        $item['body'] .= '&nbsp;';
                                    }
                                }
                                else{
                                    $item['body'] .= '<div class="btn-group">';
                                    $item['body'] .= '<a class="btn btn-small sonata-action-element" onclick="edit_duty_record(\''.$dutyItem['duty_id'][0].'\', \''.$dutyItem['branch_id'][0].'\', \''.$dutyItem['manager_id'][0].'\', \''.$dutyItem['duty_agent_id'][0].'\', \''.$dutyItem['phone'][0].'\', \''.$dutyItem['duty_day'][0].'\', \''.$dutyItem['duty_month'][0].'\', \''.$dutyItem['duty_year'][0].'\', \''.$dutyItem['duty_time_start'][0].'\', \''.$dutyItem['duty_time_end'][0].'\')">';
                                    $item['body'] .= '<i class="icon-edit"></i>';
                                    $item['body'] .= '</a>';

                                    $item['body'] .= '<a class="btn btn-small sonata-action-element" href="'.$this->generateUrl('admin_sonata_user_dutyinbranches_delete', array_merge(['id' => $dutyItem['duty_id'][0]], $delete_url_params)).'">';
                                    $item['body'] .= '<i class="icon-remove"></i>';
                                    $item['body'] .= '</a>';

                                    if($branch->getDutyAgentsCount() > 1){
                                        $item['body'] .= '<a class="btn btn-small sonata-action-element" onclick="add_duty_record_by_time('.$branch->getId().', \''.$dateItem->format('d').'\', \''.$dateItem->format('m').'\', \''.$dateItem->format('Y').'\', \''.$i.'\')">';
                                        $item['body'] .= '<i class="icon-plus"></i>';
                                        $item['body'] .= '</a>';
                                    }

                                    $item['body'] .= '</div>';
                                }

                                $item['body'] .= '</td>';
                            }
                        }
                        else{
                            $item['body'] .= '<td style="text-align: center; vertical-align: middle;">&nbsp;</td>';
                            $item['body'] .= '<td style="text-align: center; vertical-align: middle;">';
                            $item['body'] .= '<div class="btn-group">';
                            $item['body'] .= '<a class="btn btn-small sonata-action-element" onclick="add_duty_record_by_time('.$branch->getId().', \''.$dateItem->format('d').'\', \''.$dateItem->format('m').'\', \''.$dateItem->format('Y').'\', \''.$i.'\')">';
                            $item['body'] .= '<i class="icon-plus"></i>';
                            $item['body'] .= '</a>';
                            $item['body'] .= '</td>';
                        }
                    }

                    $item['body'] .= '</tr>';
                }
                $item['body'] .= '</table>';

                $item['body'] .= '</div>';
            }

            $dutyRecordCount = $dutyTime * $totalDutyAgents;
            $duty_count_by_date = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:DutyInBranches')
                ->getDutyCountByDate($dateItem, $request->query->has('branch_id') ? $request->query->get('branch_id') : null);
            $dutyFilledOn = $duty_count_by_date / $dutyRecordCount;

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
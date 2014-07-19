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
            $managers = $user;

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

            $this->getDoctrine()->getManager()->persist($duty);
            $this->getDoctrine()->getManager()->flush();
        }

        return $response;
    }
} 
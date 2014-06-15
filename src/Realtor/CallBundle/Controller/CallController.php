<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 24.04.14
 * Time: 17:15
 */

namespace Realtor\CallBundle\Controller;

use Realtor\CallBundle\Entity\BlackList;
use Realtor\CallBundle\Entity\Call;
use Realtor\CallBundle\Entity\CallMessages;
use Realtor\CallBundle\Entity\CallParams;
use Realtor\CallBundle\Entity\UserPhones;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CallController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/call/outcome/ajax", name="call_outcome_ajax")
     * @Method({"POST"})
     */
    public function ajaxCallAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()){
            return new Response(null, 403);
        }

        $request = $request->request;
        $response = new Response();

        $em = $this->getDoctrine()->getManager();

        $callManager = $this->container->get('manager.call.action');

        $uniqueId = md5(uniqid(rand(),1));

        $action = 'dial';
        if($request->has('to_phone') && $request->has('from_phone')){
            if(!$callManager->dial($request->get('from_phone'), $request->get('to_phone'), $uniqueId)){
                $response->setStatusCode(403);
            }

            $call = new Call();
            $call
                ->setFromPhone($request->get('from_phone'))
                ->setToPhone($request->get('to_phone'))
                ->setType(0)
                ->setCallAction($action)
                ->setInternalId($uniqueId)
                ->setEventAt(new \DateTime());

            $this->getDoctrine()->getManager()->persist($call);
            $this->getDoctrine()->getManager()->flush();

            return $response;
        }
        elseif($request->has('action') && $request->has('receiver')){
            if($request->get('action') == 'info_call_cancel' || $request->get('action') == 'call_cancel'){
                $dial = $this->getDoctrine()->getManager()->getRepository('CallBundle:Call')
                    ->findOneBy(['linkedId' => $request->get('linked_id'), 'callAction' => 'dial-exten']);

                if(!$dial){
                    return new Response(null, 403);
                }

                $action = $this->container->getParameter('call.action.'.$request->get('receiver'));

                if(!$callManager->bxfer($dial->getAtsCallId(), 'A', $action)){
                    $response->setStatusCode(403);
                }

                $call = new Call();
                $call
                    ->setFromPhone($dial->getToPhone())
                    ->setToPhone($action)
                    ->setType(0)
                    ->setCallAction($action)
                    ->setEventAt(new \DateTime());

                if($request->get('action') == 'call_cancel'){
                    $call->setCallResult($this->getDoctrine()->getManager()->getRepository('DictionaryBundle:CallResult')->find($request->get('call_result')));
                }

                $this->getDoctrine()->getManager()->persist($call);
                $this->getDoctrine()->getManager()->flush();
            }

            return $response;
        }
        elseif($request->has('action') && $request->has('enable') && $request->has('service_sender') && $request->has('service_receiver')){
            if($request->get('action') == 'trnf'){
                $callManager->tnf($request->get('service_sender'), $request->get('enable') == 0 ? '' :$request->get('service_receiver'));
            }
            elseif($request->get('action') == 'dnd'){
                $callManager->dnd($request->get('service_sender'), $request->get('enable'));
            }
            else{
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $call = new Call();
            $call
                ->setFromPhone($request->get('service_sender'))
                ->setToPhone($request->get('action'))
                ->setType(0)
                ->setCallAction($request->get('action'))
                ->setEventAt(new \DateTime());

            $this->getDoctrine()->getManager()->persist($call);
            $this->getDoctrine()->getManager()->flush();

            return $response;
        }
        elseif($request->has('CallCard')){
            $params = $request->get('CallCard');

            if($this->container->get('kernel')->getEnvironment() != 'dev'){
                $criteria = [
                    'linkedId' => $params['linked-id'],
                    'callAction' => 'dial-exten'
                ];

                if(preg_match('/^\d+$/', $params['caller-phone'])){
                    $criteria['toPhone'] = $params['caller-phone'];
                }

                $dial = $this->getDoctrine()->getManager()->getRepository('CallBundle:Call')
                    ->findOneBy($criteria);
            }
            else{
                $dial = new Call();
                $dial->setAtsCallId($uniqueId);
            }

            if(!$dial){
                return new Response(null, 403);
            }

            switch($params['action']){
                case 'black-list-forward': //занести в черный список
                    $blackList = new BlackList();
                    $blackList
                        ->setPhone($params['from-phone'])
                        ->setUserId($em->getRepository('ApplicationSonataUserBundle:User')->find($params['user-id']))
                        ->setReason($params['message'])
                        ->setDialId($dial->getAtsCallId());
                    $em->persist($blackList);

                    $action = 'bl';
                    break;
                case 'office-random-phone-forward': //Случайный вызов
                    $action = $this->container->getParameter('call.action.random');

                    if(!$callManager->bxfer($dial->getAtsCallId(), 'A', $action)){
                        $response->setStatusCode(403);
                    }

                    if(empty($params['call-to-phone'])){
                        $params['call-to-phone'] = $action;
                    }

                    if(empty($params['caller-name'])){
                        $params['caller-name'] = 'unknown';
                    }
                    break;
                case 'forward-to-call-center':
                    if(empty($params['caller-name'])){
                        $params['caller-name'] = 'unknown';
                    }

                    $action = $this->container->getParameter('call.action.operator');

                    if(!$callManager->bxfer($dial->getAtsCallId(), 'A', $action)){
                        $response->setStatusCode(403);
                    }
                    break;
                case 'user-add-phone':
                case 'user-replace-phone':
                    $action = $params['action'];

                    $userPhone = new UserPhones();

                    if($action == 'user-replace-phone'){
                        $this->getDoctrine()->getManager()->getRepository('CallBundle:UserPhones')->removePhonesByUser($params['user-id']);
                    }

                    $userPhone
                        ->setPhone($params['user-call-phone'])
                        ->setUserId($this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')->find($params['user-id']))
                        ->setAppendedUserId($this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')->find($params['appended-user-id']))
                        ->setPhoneAction($action)
                        ->setDialId($dial->getAtsCallId());

                    $this->getDoctrine()->getManager()->persist($userPhone);
                    $em->flush();

                    break;
                default:
                    $action = 'bxfer';

                    if(!$callManager->bxfer($dial->getAtsCallId(), 'A', $params['call-to-phone'])){
                        $response->setStatusCode(403);
                    }

                    break;
            }
        }

        $call = new Call();
        $callParams = new CallParams();

        if(isset($params['advertising-source']) && !empty($params['advertising-source'])){
            $callParams->setAdvertisingSource($em->getRepository('DictionaryBundle:AdvertisingSource')->find($params['advertising-source']));
        }

        $callParams->setCallerName($params['caller-name']);
        $callParams->setCallType($params['call-type']);

        if(!empty($params['message'])){
            $callMessage = new CallMessages();
            $callMessage
                ->setCallParamId($callParams)
                ->setMessage($params['message']);
            $callParams->addMessage($callMessage);

            $this->getDoctrine()->getManager()->persist($callMessage);
        }

        if(isset($params['property']) && !empty($params['property'])){
            $callParams
                ->setPropertyAddress($params['property'])
                ->setPropertyAgentId($em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(['outerId' => $params['property-agent-id']]))
                ->setPropertyId($params['property-id'])
                ->setPropertyBaseId($params['property-base-id']);
        }

        if(isset($params['reason']) && !empty($params['reason'])){
            $callParams->setReason($em->getRepository('DictionaryBundle:Reason')->find($params['reason']));
        }

        if(!empty($params['branch-to-call'])){
            $callParams->setBranch($em->getRepository('DictionaryBundle:Branches')->find($params['branch-to-call']));
        }

        if(!empty($params['who-call'])){
            $callParams->setOtherWhoCall($em->getRepository('DictionaryBundle:Callers')->find($params['who-call']));
        }

        $initCall = null;
        if(!empty($params['call-id'])){
            $initCall = $em->getRepository('CallBundle:Call')->find($params['call-id']);
        }

        $call
            ->setFromPhone($params['caller-phone'])
            ->setToPhone($params['call-to-phone'])
            ->setType(0)
            ->setCallAction($action)
            ->setInternalId($uniqueId)
            ->setEventAt(new \DateTime())
            ->setLinkedId($params['linked-id']);

        if($initCall){
            $callParams->setCallId($initCall);
            $initCall->setParams($callParams);

            $em->persist($initCall);
        }
        else{
            $callParams->setCallId($call);
            $call->setParams($callParams);
        }

        $em->persist($call);
        $em->persist($callParams);
        $em->flush();

        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/call/get_income_event/ajax", name="call_get_income_event_ajax")
     * @Method({"POST"})
     */
    public function getIncomeEventAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()){
            return new Response(null, 403);
        }

        if(!$request->request->has('to_phone') || !$forPhone = $request->request->get('to_phone')){
            return new Response(null, 403);
        }

        if(!$call = $this->getDoctrine()->getManager()->getRepository('CallBundle:Call')
            ->getIncomeCall($forPhone, $this->container->getParameter('call.income.event'))){
            return new Response(null, 403);
        }

        $firstCall = $this->getDoctrine()->getManager()->getRepository('CallBundle:Call')->findOneBy(['linkedId' => $call[0]['linkedId'], 'callAction' => 'connect-exten']);

        if($firstCall){
            $call[0]['caller_default'] = $firstCall->getFromPhone();

            if($firstCall->getParams()){
                if($firstCall->getParams()->getCallerName()){
                    $call[0]['callerName'] = $firstCall->getParams()->getCallerName();
                }

                if($firstCall->getParams()->getAdvertisingSource()){
                    $call[0]['advertisingSource'] = $firstCall->getParams()->getAdvertisingSource()->getId();
                }

                if($firstCall->getParams()->getPropertyId()){
                    $call[0]['propertyId'] = $firstCall->getParams()->getPropertyId();
                }

                if($firstCall->getParams()->getPropertyAddress()){
                    $call[0]['propertyAddress'] = $firstCall->getParams()->getPropertyAddress();
                }

                if($firstCall->getParams()->getPropertyAgentId()){
                    $call[0]['propertyAgentId'] = $firstCall->getParams()->getPropertyAgentId()->getId();
                }

                if($firstCall->getParams()->getPropertyBaseId()){
                    $call[0]['propertyBaseId'] = $firstCall->getParams()->getPropertyBaseId();
                }

                if($firstCall->getParams()->getReason()){
                    $call[0]['reason'] = $firstCall->getParams()->getReason()->getId();
                }

                if($firstCall->getParams()->getMessage()){
                    $allCall = $this->getDoctrine()->getManager()->getRepository('CallBundle:Call')->findAll(['linkedId' => $call[0]['linkedId']]);

                    foreach($allCall as $item){
                        if($item->getParams()){
                            if($item->getParams()->getMessage()){
                                foreach($item->getParams()->getMessage() as $message){
                                    $call[0]['message'][] = $message->getMessage();
                                }
                            }
                        }
                    }
                }

                if($firstCall->getParams()->getOtherWhoCall()){
                    $call[0]['who_call'] = $firstCall->getParams()->getOtherWhoCall()->getId();
                }

                $call[0]['callType'] = $firstCall->getParams()->getCallType();
            }
        }

        return new Response(json_encode($call[0]));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/call/event/is_authenticated_by_access_code_user", name="is_authenticated_by_access_code_user")
     * @Method({"POST"})
     */
    public function isAuthenticatedByAccessCodeUserAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()){
            return new JsonResponse(['success' => false]);
        }

        if(!$request->request->has('access_code') || !$request->request->has('user_id')){
            return new JsonResponse(['success' => false]);
        }

        $auth = $this->getDoctrine()->getManager()->getRepository('CallBundle:Call')
            ->getAuthByAccessCodeEvent($request->request->get('access_code'));

        if(!$auth || count($auth) == 0){
            return new JsonResponse(['success' => false]);
        }

        if(!$user = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')->find($request->request->get('user_id'))){
            return new JsonResponse(['success' => false]);
        }

        $user->setUserDutyPhone($auth[0]->getFromPhone());

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(
            [
                'success' => true,
                'event_id' => $auth[0]->getId()
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/call/event/income", name="call_event_income")
     * @Method({"POST"})
     */
    public function eventIncomeCallController(Request $request)
    {
        $request = $request->request;
        $em = $this->getDoctrine()->getManager();

        $call = new Call();

        if($request->has('uuid') && $uuid = $request->get('uuid')){
            $call->setInternalId($uuid);
        }

        if($request->has('linkedid') && $linkedId = $request->get('linkedid')){
            $call->setLinkedId($linkedId);
        }

        if($request->has('id') && $id = $request->get('id')){
            $call->setAtsCallId($id);
        }

        if($request->has('time') && $time = $request->get('time')){
            $call->setEventAt(new \DateTime($time));
        }

        if($request->has('pincode') && $accessCode = $request->get('pincode')){
            $call->setAccessCode($accessCode);
        }

        $call
            ->setType(1)
            ->setCallAction($request->get('event'))
            ->setFromPhone($request->get('cid'))
            ->setToPhone($request->get('did'));

        $em->persist($call);
        $em->flush();

        return new Response();
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     *
     * @Route("/get/user/branch/by/phone/ajax", name="get_user_branch_by_phone_ajax")
     * @Method({"GET"})
     */
    public function getUserAndBranchesByPhone(Request $request)
    {
        $response = new JsonResponse();

        if(!$request->isXmlHttpRequest()){
            return $response->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        if(!$request->query->has('term')){
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $users = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')->getUserByPhone($request->query->get('term'));

        $responseData = [];

        if($users){
            foreach($users as $user){
                $responseData[] = [
                    'id' => $user->getId(),
                    'branch_name' => $user->getBranch()->getName(),
                    'name' => $user->getFio(),
                    'phone' => $user->getPhone(),
                    'office_phone' => $user->getOfficePhone(),
                    'may_trans_to_cell_phone' => $user->getMayRedirectCall(),
                    'manager_office_phone' => $user->getHead() ? $user->getHead()->getOfficePhone() : false,
                    'branch_phone' => $user->getBranch()->getBranchPhone()
                ];
            }
        }

        $branches = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')->getBranchByPhone($request->query->get('term'));

        if($branches){
            foreach($branches as $branch){
                $responseData[] = [
                    'id' => $branch->getId(),
                    'name' => $branch->getName(),
                    'phone' => $branch->getBranchNumber()
                ];
            }
        }

        if(empty($responseData)){
            return $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        return $response->setData($responseData);
    }
} 
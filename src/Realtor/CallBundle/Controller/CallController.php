<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 24.04.14
 * Time: 17:15
 */

namespace Realtor\CallBundle\Controller;

use Guzzle\Http\Exception\RequestException;
use Realtor\CallBundle\Entity\BlackList;
use Realtor\CallBundle\Entity\Call;
use Realtor\CallBundle\Entity\CallParams;
use Realtor\DictionaryBundle\Model\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

            return $response;
        }
        elseif($request->has('action') && $request->has('receiver')){
            if($request->get('action') == 'info_call_cancel' || $request->get('action') == 'call_cancel'){
                $action = $this->container->getParameter('call.action.'.$request->get('receiver'));

                if(!$callManager->bxfer($request->get('linked_id'), 'A', $action)){
                    $response->setStatusCode(403);
                }
            }

            return $response;
        }
        elseif($request->has('CallCard')){
            $params = $request->get('CallCard');

            $dial = $this->getDoctrine()->getManager()->getRepository('CallBundle:Call')
                ->findOneBy(['linkedId' => $params['linked-id'], 'callAction' => 'dial-exten']);

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

                    if(!$callManager->blackList($dial->getLinkedId(), 1)){
                        $response->setStatusCode(403);
                    }

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

        if(isset($params['advertising-source'])){
            $callParams->setAdvertisingSource($em->getRepository('DictionaryBundle:AdvertisingSource')->find($params['advertising-source']));
        }

        $callParams->setCallerName($params['caller-name']);
        $callParams->setCallType($params['call-type']);

        if(!empty($params['message'])){
            $callParams->setMessage($params['message']);
        }

        if(isset($params['property'])){
            $callParams
                ->setPropertyAddress($params['property'])
                ->setPropertyAgentId($em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(['outerId' => $params['property-agent-id']]))
                ->setPropertyId($params['property-id'])
                ->setPropertyBaseId($params['property-base-id']);
        }

        if(isset($params['reason'])){
            $callParams->setReason($em->getRepository('DictionaryBundle:Reason')->find($params['reason']));
        }

        if(!empty($params['branch-to-call'])){
            $callParams->setBranch($em->getRepository('DictionaryBundle:Branches')->find($params['branch-to-call']));
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

        return new Response(json_encode($call[0]));
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
     * @return Response
     *
     * @Route("/event/to/black_list", name="event_to_black_list")
     * @Method({"POST"})
     */
    public function addToBlackList(Request $request)
    {
        return new Response();
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 24.04.14
 * Time: 17:15
 */

namespace Realtor\CallBundle\Controller;

use Guzzle\Http\Exception\RequestException;
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
        $responseInstance = new Response();

        $uniqueId = md5(uniqid(rand(),1));

        if($request->has('to_phone') && $request->has('from_phone')){
            $em = $this->getDoctrine()->getManager();

            $to = $request->get('to_phone');

            $httpClient = (new HttpClient())->getClient();
            try{
                $response = $httpClient->post(
                    'http://188.227.101.17:8080',
                    [],
                    ['action' => 'dial', 'cid' => $request->get('from_phone'), 'did' => $to, 'uuid' => $uniqueId]
                )->send();

                $response = [
                    'call_state' => true,
                    'call_comment' => print_r($response->getStatusCode(), true)
                ];

                $call = new Call();
                $call
                    ->setFromPhone($request->get('from_phone'))
                    ->setToPhone($to)
                    ->setType(0)
                    ->setCallAction('dial')
                    ->setInternalId($uniqueId)
                    ->setEventAt(new \DateTime());

                $em->persist($call);
                $em->flush();
            }
            catch(RequestException $e){
                $response = [
                    'call_state' => false,
                    'call_comment' => $e->getMessage()
                ];
            }

            $responseInstance->setContent(json_encode($response));
        }
        elseif($request->has('CallCard') && $request->has('CallCard')){
            $callCard = $request->get('CallCard');

            if(!$callCard['linked-id'] || !$callCard['call-id']){
                return new Response(null, 403);
            }

            $em = $this->getDoctrine()->getManager();

            $call = new Call();
            $callParams = new CallParams();

            if($callCard['action'] == 'agent-office-forward'){
                $to = $callCard['agent-office-phone'];
            }
            elseif($callCard['action'] == 'agent-cell-forward'){
                $to = $callCard['agent-cell-phone'];
            }
            else{
                $to = $callCard['agent-phone'];
            }

            $initCall = $em->getRepository('CallBundle:Call')->find($callCard['call-id']);

            $callParams
                ->setAdvertisingSource($em->getRepository('DictionaryBundle:AdvertisingSource')->find($callCard['advertising-source']))
                ->setCallerName($callCard['caller-name'])
                ->setCallType($callCard['call-type'])
                ->setMessage($callCard['message'])
                ->setPropertyAddress($callCard['property'])
                ->setPropertyAgentId($em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(['outerId' => $callCard['property-agent-id']]))
                ->setPropertyId($callCard['property-id'])
                ->setPropertyBaseId($callCard['property-base-id'])
                ->setReason($em->getRepository('DictionaryBundle:Reason')->find($callCard['reason']));
            $call
                ->setFromPhone($callCard['caller-phone'])
                ->setToPhone($to)
                ->setType(0)
                ->setCallAction('bxfer')
                ->setInternalId($uniqueId)
                ->setEventAt(new \DateTime())
                ->setLinkedId($callCard['linked-id']);

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

            $httpClient = (new HttpClient())->getClient();

            try{
                $r = $httpClient->post(
                    'http://188.227.101.17:8080',
                    [],
                    [
                        'action' => 'bxfer',
                        'linkedid' => $callCard['linked-id'],
                        'leg' => 'A',
                        'did' => $to
                    ]
                )->send();
            }
            catch(RequestException $e){
                $responseInstance->setStatusCode(403);
            }
        }

        return $responseInstance;
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
} 
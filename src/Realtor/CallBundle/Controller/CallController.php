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
            return new Response(403);
        }

        $request = $request->request;
        $responseInstance = new Response();

        if($request->has('to_phone') && $request->has('from_phone')){
            $em = $this->getDoctrine()->getManager();

            $to = $request->get('to_phone');

            $httpClient = (new HttpClient())->getClient();
            try{
                $uniqueId = md5(uniqid(rand(),1));

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

        return $responseInstance;
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
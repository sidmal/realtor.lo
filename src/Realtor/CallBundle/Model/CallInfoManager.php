<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 28.07.14
 * Time: 22:55
 */

namespace Realtor\CallBundle\Model;

use Doctrine\ORM\EntityManager;
use Guzzle\Http\Client;
use Psr\Log\LoggerInterface;
use Realtor\CallBundle\Exceptions\CallInfoException;
use Realtor\CallBundle\Plugin\SimpleXMLElementExtended;

class CallInfoManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entity_manager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    private $url;

    public function __construct(EntityManager $entity_manager, LoggerInterface $logger, $url)
    {
        if(empty($url)){
            throw new CallInfoException('emls url for send call info not set.');
        }

        $this->entity_manager = $entity_manager;
        $this->logger = $logger;
        $this->url = $url;
    }

    public function send_call_info($call_linked_id)
    {
        $call = $this->entity_manager->getRepository('CallBundle:Call')->findOneBy(
            [
                'linkedId' => $call_linked_id,
                'callAction' => 'connect-exten'
            ]
        );

        if(!$call){
            throw new CallInfoException('call by linked id not found.');
        }

        $request = new SimpleXMLElementExtended('<request/>');
        $request->addChild('id', $call->getId());
        $request->addChild('from_phone', $call->getFromPhone());
        $request->addChild('to_phone', $call->getToPhone());
        $request->addChild('call_date', $call->getCreatedAt()->format('Y-m-d\TH:i:s'));

        if($call->getCallResult()){
            $request->addCData('call_result', $call->getCallResult()->getName());
        }

        if($call->getParams()){
            $call_params = $request->addChild('call_params');

            if(!is_null($call->getParams()->getCallType())){
                if($call->getParams()->getCallType() == 0){
                    $call_type = 'По объекту';
                }
                elseif($call->getParams()->getCallType() == 1){
                    $call_type = 'Нет объекта';
                }
                elseif($call->getParams()->getCallType() == 2){
                    $call_type = 'Звонок сотрудника';
                }
                elseif($call->getParams()->getCallType() == 3){
                    $call_type = 'Другое';
                }

                $call_params->addCData('call_type', $call_type);
            }

            if($call->getParams()->getAdvertisingSource()){
                $call_params->addCData('advertising_source', $call->getParams()->getAdvertisingSource()->getName());
            }

            if($call->getParams()->getPropertyId()){
                $property = $call_params->addChild('property');

                $property->addChild('id', $call->getParams()->getPropertyId());
                $property->addCData('address', trim($call->getParams()->getPropertyAddress()));
                $property->addCData('agent', $call->getParams()->getPropertyAgentId()->getFio());
            }

            if($call->getParams()->getReason()){
                $call_params->addCData('reason', $call->getParams()->getReason()->getName());
            }

            if($call->getParams()->getBranch()){
                $call_params->addCData('branch', $call->getParams()->getBranch()->getName());
            }

            if($call->getParams()->getCallerName()){
                if(in_array($call->getParams()->getCallerName(), ['unknown', '<unknown>'])){
                    $caller_name = 'Отказался представиться';
                }
                else{
                    $caller_name = $call->getParams()->getCallerName();
                }

                $call_params->addCData('caller_name', $caller_name);
            }

            if($call->getParams()->getOtherWhoCall()){
                $call_params->addCData('who_call', $call->getParams()->getOtherWhoCall()->getName());
            }

            if($call->getParams()->getMessage()){
                $messages = $call_params->addChild('messages');

                foreach($call->getParams()->getMessage() as $message){
                    $m = $messages->addChild('message');

                    $m->addCdata('text', $message->getMessage());
                    $m->addChild('date', $message->getCreatedAt()->format('Y-m-d\TH:i:s'));
                }
            }
        }

        $http_client = new Client($this->url);
        $http_request = $http_client->post(null, null, $request->asXML());

        try{
            $http_response = $http_request->send();
        }
        catch(\Exception $e){}

        $message = [
            'url' => $this->url,
            'method' => 'post',
            'request' => $request->asXML(),
            'response' => isset($http_response) ?
                    [
                        'headers' => $http_response->getRawHeaders(),
                        'body' => $http_response->getBody(true)
                    ]
                    : $e->getMessage()
        ];

        echo $request->asXML();

        $this->logger->debug(json_encode($message));

        return true;
    }
} 
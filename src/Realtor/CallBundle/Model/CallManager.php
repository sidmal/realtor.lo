<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 11.05.14
 * Time: 20:46
 */

namespace Realtor\CallBundle\Model;

use Guzzle\Http\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Realtor\CallBundle\Exceptions\CallException;
use Realtor\DictionaryBundle\Model\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class CallManager
{
    /**
     * @var \Guzzle\Http\Message\RequestInterface
     */
    private $httpClient;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct($url, LoggerInterface $logger)
    {
        if(empty($url)){
            throw new CallException('url for send request to ats not set.');
        }

        $this->httpClient = (new HttpClient())->getClient()->post($url);
        $this->logger = $logger;
    }

    public function dial($sender, $receiver, $uniqueId)
    {
        $this->httpClient->setPostField('action', 'dial');
        $this->httpClient->setPostField('cid', $sender);
        $this->httpClient->setPostField('did', $receiver);
        $this->httpClient->setPostField('uuid', $uniqueId);

        return $this->sendCall();
    }

    public function bxfer($linkedId, $leg, $receiver)
    {
        $this->httpClient->setPostField('action', 'bxfer');
        $this->httpClient->setPostField('linkedid', $linkedId);
        $this->httpClient->setPostField('leg', $leg);
        $this->httpClient->setPostField('did', $receiver);

        return $this->sendCall();
    }

    public function blackList($uniqueId, $enable)
    {
        $this->httpClient->setPostField('action', 'bl');
        $this->httpClient->setPostField('uniqueid', $uniqueId);
        $this->httpClient->setPostField('enable', $enable);

        return $this->sendCall();
    }

    public function dnd($sender, $enable)
    {
        $this->httpClient->setPostField('action', 'dnd');
        $this->httpClient->setPostField('cid', $sender);
        $this->httpClient->setPostField('enable', $enable);

        return $this->sendCall();
    }

    public function tnf($sender, $receiver = '')
    {
        $this->httpClient->setPostField('action', 'trnf');
        $this->httpClient->setPostField('cid', $sender);
        $this->httpClient->setPostField('did', $receiver);

        return $this->sendCall();
    }

    public function dndCheck($sender)
    {
        $this->httpClient->setPostField('action', 'dnd');
        $this->httpClient->setPostField('cid', $sender);
        $this->httpClient->setPostField('check', 1);

        return $this->sendCall();
    }

    public function tnfCheck($sender)
    {
        $this->httpClient->setPostField('action', 'trnf');
        $this->httpClient->setPostField('cid', $sender);
        $this->httpClient->setPostField('check', 1);

        return $this->sendCall();
    }

    /**
     * @return bool
     */
    protected function sendCall()
    {
        try {
            $response = $this->httpClient->send();

            $result = true;

            if($response->getStatusCode() != Response::HTTP_OK){
                return false;
            }

            if($response->hasHeader('Result')){
                print_r($response->getHeader('Result')->toArray());
                if((integer)$response->getHeader('Result') == 0){
                    $result = false;
                }
                else{
                    $result = true;
                }
            }
        }
        catch(RequestException $e){
            $result = false;
        }

        $postData = $this->httpClient->getPostFields()->getAll();

        $message = [
            'url' => $this->httpClient->getUrl(),
            'method' => 'post',
            'request' => $postData,
            'response' => isset($response) ?
                [
                    'headers' => $response->getHeaders()->toArray(),
                    'body' => $response->getBody(true)
                ]
                : $e->getMessage()
        ];

        $this->logger->debug(json_encode($message));

        return $result;
    }
} 
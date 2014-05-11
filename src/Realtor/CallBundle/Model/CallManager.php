<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 11.05.14
 * Time: 20:46
 */

namespace Realtor\CallBundle\Model;

use Guzzle\Http\Exception\RequestException;
use Realtor\CallBundle\Exceptions\CallException;
use Realtor\DictionaryBundle\Model\HttpClient;

class CallManager
{
    /**
     * @var \Guzzle\Http\Message\RequestInterface
     */
    private $httpClient;

    public function __construct($url)
    {
        if(empty($url)){
            throw new CallException('url for send request to ats not set.');
        }

        $this->httpClient = (new HttpClient())->getClient()->post($url);
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

    /**
     * @return bool
     */
    protected function sendCall()
    {
        try {
            $this->httpClient->send();

            $result = true;
        }
        catch(RequestException $e){
            echo $e->getMessage();

            $result = false;
        }

        return $result;
    }
} 
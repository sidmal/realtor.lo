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
        $request = $this->httpClient->getQuery();

        $request->set('action', 'dial');
        $request->set('cid', $sender);
        $request->set('did', $receiver);
        $request->set('uuid', $uniqueId);

        return $this->sendCall();
    }

    public function bxfer($linkedId, $leg, $receiver)
    {
        $request = $this->httpClient->getQuery();

        $request->set('action', 'bxfer');
        $request->set('linkedid', $linkedId);
        $request->set('leg', $leg);
        $request->set('did', $receiver);

        return $this->sendCall();
    }

    public function blackList($uniqueId, $enable)
    {
        $request = $this->httpClient->getQuery();

        $request->set('action', 'bl');
        $request->set('uniqueid', $uniqueId);
        $request->set('enable', $enable);

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
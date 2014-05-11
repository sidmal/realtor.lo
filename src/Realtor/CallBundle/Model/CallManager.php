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

        try{
            $this->httpClient->send();

            $result = true;
        }
        catch(RequestException $e){
            $result = false;
        }

        return $result;
    }

    public function bxfer($linkedId, $leg, $receiver)
    {
        $request = $this->httpClient->getQuery();

        $request->set('action', 'bxfer');
        $request->set('linkedid', $linkedId);
        $request->set('leg', $leg);
        $request->set('did', $receiver);

        try{
            $this->httpClient->send();

            $result = true;
        }
        catch(RequestException $e){
            $result = false;
        }

        return $result;
    }

    public function blackList($uniqueId, $enable)
    {
        $request = $this->httpClient->getQuery();

        $request->set('action', 'bl');
        $request->set('uniqueid', $uniqueId);
        $request->set('enable', $enable);

        try{
            $this->httpClient->send();

            $result = true;
        }
        catch(RequestException $e){
            $result = false;
        }

        return $result;
    }
} 
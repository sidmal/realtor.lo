<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 28.04.14
 * Time: 11:33
 */

namespace Realtor\DictionaryBundle\Model;

use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\RequestInterface;
use Realtor\DictionaryBundle\Exceptions\UserException;

class UserManager
{
    /**
     * @var \Guzzle\Http\Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $url;

    private $em;

    public function __construct($url, EntityManager $em)
    {
        if(empty($url)){
            new UserException('url for load users from remote service not set.');
        }

        $this->httpClient = (new HttpClient())->getClient();
        $this->url = $url;
        $this->em = $em;
    }

    /**
     * @param \Guzzle\Http\Message\RequestInterface $request
     * @return array
     */
    protected static function formatResponse(RequestInterface $request)
    {
        $response = null;

        try{
            $response = $request->send();
        }
        catch(RequestException $e){
            new UserException('request failed', $e->getCode(), $e);
        }

        $response = $response->json();

        if (!$response) {
            new UserException('response for action load user is not a valid json document.');
        }

        return $response;
    }

    public function loadUserById($userId)
    {
        $request = $this->httpClient->get($this->url.'/'.$userId, ['Accept' => 'application/json']);

        return self::formatResponse($request);
    }

    public function loadUsers()
    {
        $request = $this->httpClient->get($this->url, ['Accept' => 'application/json']);

        return self::formatResponse($request);
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 28.04.14
 * Time: 11:35
 */

namespace Realtor\DictionaryBundle\Model;

use Guzzle\Http\Client;

class HttpClient
{
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->httpClient
            ->setUserAgent('Realtor Http Client v.0.0.1')
            ->setConfig(
                [
                    'CURLOPT_CONNECTTIMEOUT' => 10,
                    'CURLOPT_TIMEOUT' => 10
                ]
            );
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->httpClient;
    }
} 
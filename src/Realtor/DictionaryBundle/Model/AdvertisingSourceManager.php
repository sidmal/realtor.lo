<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 29.03.14
 * Time: 20:39
 */

namespace Realtor\DictionaryBundle\Model;

use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Realtor\DictionaryBundle\Entity\AdvertisingSource;
use Realtor\DictionaryBundle\Exceptions\AdvertisingSourceException;

class AdvertisingSourceManager
{
    private $httpClient;

    private $url;

    private $em;

    public function __construct($url, EntityManager $em)
    {
        if(empty($url)){
            throw new AdvertisingSourceException('url for load advertising source from remote service not set.');
        }

        $this->httpClient = (new HttpClient())->getClient();
        $this->url = $url;
        $this->em = $em;
    }

    public function loadAdvertisingSource()
    {
        $request = $this->httpClient->get($this->url, ['Accept' => 'application/json']);

        $response = null;

        try{
            $response = $request->send()->json();
        }
        catch(RequestException $e){
            new AdvertisingSourceException('request failed', $e->getCode(), $e);
        }

        if(!$response){
            new AdvertisingSourceException('response for action load advertising source is not a valid json document.');
        }

        return $response;
    }

    public function save(array $item)
    {
        $advertisingSource = $this->em->getRepository('DictionaryBundle:AdvertisingSource')
            ->findOneBy(['outerId' => $item['id']]);

        if(!$advertisingSource){
            $advertisingSource = new AdvertisingSource();
        }

        $advertisingSource
            ->setOuterId($item['id'])
            ->setName($item['name'])
            ->setIsActive(((integer)$item['is_active'] == 1) ? true : false);

        $this->em->persist($advertisingSource);
        $this->em->flush($advertisingSource);

        return $advertisingSource->getId();
    }

    public function truncate()
    {
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL(
                $this->em->getClassMetadata('DictionaryBundle:AdvertisingSource')->getTableName(), true)
        );
    }
} 
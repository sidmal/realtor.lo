<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 10.05.14
 * Time: 1:09
 */

namespace Realtor\DictionaryBundle\Model;

use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Realtor\DictionaryBundle\Entity\Branches;
use Realtor\DictionaryBundle\Exceptions\BranchException;

class BranchManager
{
    private $httpClient;

    private $url;

    private $em;

    public function __construct($url, EntityManager $em)
    {
        if(empty($url)){
            throw new BranchException('url for load branch from remote service not set.');
        }

        $this->httpClient = (new HttpClient())->getClient();
        $this->url = $url;
        $this->em = $em;
    }

    public function loadBranch()
    {
        $request = $this->httpClient->get($this->url, ['Accept' => 'application/json']);

        $response = null;

        try{
            $response = $request->send()->json();
        }
        catch(RequestException $e){
            new BranchException('request failed', $e->getCode(), $e);
        }

        if(!$response){
            new BranchException('response for action load user is not a valid json document.');
        }

        return $response;
    }

    public function save(array $branchItem)
    {
        $branch = $this->em->getRepository('DictionaryBundle:Branches')
            ->findOneBy(['outerId' => $branchItem['id_office']]);

        if(!$branch){
            $branch = new Branches();
        }

        $branch
            ->setOuterId($branchItem['id_office'])
            ->setName($branchItem['office_name'])
            ->setCityPhone($branchItem['office_phone'])
            ->setAddress($branchItem['office_address'])
            ->setBranchNumber($branchItem['ext_phone'])
            ->setOnDutyAgentPhone($branchItem['duty_agent'])
            ->setDutyAgentsCount($branchItem['duty_cnt'])
            ->setMayTrans($branchItem['maytrans'] == 1 ? true : false)
            ->setIsActive(true);

        $this->em->persist($branch);
        $this->em->flush($branch);

        return $branch->getId();
    }

    public function truncate()
    {
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL(
                $this->em->getClassMetadata('DictionaryBundle:Branches')->getTableName(), true)
        );
    }
} 
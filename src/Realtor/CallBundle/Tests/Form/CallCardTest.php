<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 13.05.14
 * Time: 22:06
 */

namespace Realtor\CallBundle\Tests\Form;

use Realtor\CallBundle\Entity\Call;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CallCardTest extends WebTestCase
{
    public function testCallCardActivate()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $uniqueId = md5(uniqid(rand(), 1));

        $call = new Call();
        $call->setLinkedId('c853cd0caf00bb9b54da5024abc4889b')->setInternalId($uniqueId)->setAtsCallId($uniqueId)
            ->setType(1)->setFromPhone('9213839210')->setToPhone('205')->setCallAction('connect-exten')
            ->setEventAt(new \DateTime());

        $em->persist($call);
        $em->flush();
    }

    public function testDialExten()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $uniqueId = md5(uniqid(rand(), 1));

        $call = new Call();
        $call->setLinkedId($uniqueId)->setInternalId($uniqueId)->setAtsCallId($uniqueId)
            ->setType(1)->setFromPhone('9213839209')->setToPhone('205')->setCallAction('dial-exten')
            ->setEventAt(new \DateTime());

        $em->persist($call);
        $em->flush();

        echo $uniqueId;
    }

    public function testAuthAccessCode()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $uniqueId = md5(uniqid(rand(), 1));

        $call = new Call();
        $call->setLinkedId($uniqueId)->setInternalId($uniqueId)->setAtsCallId($uniqueId)
            ->setType(1)->setFromPhone('205')->setToPhone('000')->setCallAction('pincode')
            ->setEventAt(new \DateTime())->setAccessCode('7502');

        $em->persist($call);
        $em->flush();
    }
} 
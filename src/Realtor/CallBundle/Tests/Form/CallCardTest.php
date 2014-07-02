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
        $call->setLinkedId('a5227340611c5cb917558893f4c60e9a')->setInternalId($uniqueId)->setAtsCallId($uniqueId)
            ->setType(1)->setFromPhone('9219251984')->setToPhone('205')->setCallAction('connect-exten')
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
            ->setType(1)->setFromPhone('9219251984')->setToPhone('205')->setCallAction('dial-exten')
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
            ->setEventAt(new \DateTime())->setAccessCode('6142');

        $em->persist($call);
        $em->flush();
    }
} 
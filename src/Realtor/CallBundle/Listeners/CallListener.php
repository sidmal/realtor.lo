<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 18.05.14
 * Time: 0:34
 */

namespace Realtor\CallBundle\Listeners;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Realtor\CallBundle\Entity\BlackList;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CallListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof BlackList){
            $this->container->get('manager.call.action')->blackList($entity->getDialId(), 0);
        }
    }
} 
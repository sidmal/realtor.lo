<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 16.06.14
 * Time: 0:43
 */

namespace Realtor\AdminBundle\Controller;

use Realtor\CallBundle\Entity\Call;
use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class UserPhonesCRUDController extends CRUDController
{
    /**
     * @Route("/verify", name="verify_user_phone")
     */
    public function verifyUserPhoneAction()
    {
        $response = new Response();

        if(!$this->get('request')->isXmlHttpRequest()){
            return $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if(!$object){
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        if($object->getPhoneBeforeReplace()){
            if(!$this->container->get('manager.call.action')->rl($object->getDialId(), 0)){
                return $response->setStatusCode(Response::HTTP_FORBIDDEN);
            }
        }

        if(!$this->container->get('manager.call.action')->rl($object->getDialId(), 1)){
            return $response->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $user = $object->getUserId();

        if($object->getPhoneAction() == 'user-add-phone'){
            $user->setPhone($user->getPhone().', '.$object->getPhone());
        }
        else{
            $user->setPhone($object->getPhone());
        }

        $object->setIsVerify(true);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($object);
        $this->getDoctrine()->getManager()->flush();

        return $response;
    }

    public function deleteAction($id)
    {
        $call = new Call();
        $user = $this->getUser();
        $object = $this->admin->getObject($id);

        $call
            ->setFromPhone($user->getUserDutyPhone())
            ->setToPhone($object->getPhone())
            ->setType(1)
            ->setCallAction('rl')
            ->setInternalId(md5(uniqid(rand(),1)))
            ->setEventAt(new \DateTime());
        $this->getDoctrine()->getManager()->persist($call);
        $this->getDoctrine()->getManager()->flush();

        $this->container->get('manager.call.action')->rl($object->getDialId(), 0);

        return parent::deleteAction($id);
    }

    /**
     * @Route("/call", name="call_to_user_phone")
     */
    public function callToUserPhoneAction($id)
    {
        $response = new Response();

        $user = $this->getUser();
        $object = $this->admin->getObject($id);

        if(!$this->get('request')->isXmlHttpRequest()){
            return $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if(!$object || !$object->getPhone()){
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $uniqueCallId = md5(uniqid(rand(),1));
        if(!$this->container->get('manager.call.action')->dial($user->getUserDutyPhone(), $object->getPhone(), $uniqueCallId)){
            return $response->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $call = new Call();
        $call
            ->setFromPhone($user->getUserDutyPhone())
            ->setToPhone($object->getPhone())
            ->setType(1)
            ->setCallAction('dial')
            ->setInternalId($uniqueCallId)
            ->setEventAt(new \DateTime());
        $this->getDoctrine()->getManager()->persist($call);
        $this->getDoctrine()->getManager()->flush();

        return $response;
    }
}
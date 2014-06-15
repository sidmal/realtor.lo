<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 16.06.14
 * Time: 0:43
 */

namespace Realtor\AdminBundle\Controller;

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
}
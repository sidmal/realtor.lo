<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 09.06.14
 * Time: 1:21
 */

namespace Realtor\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlackListCRUDController extends CRUDController
{
    /**
     * @Route("/verify", name="verify")
     */
    public function verifyAction()
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

        if(!$this->container->get('manager.call.action')->blackList($object->getDialId(), 1)){
            return $response->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        $object->setIsVerify(true);
        $this->getDoctrine()->getManager()->persist($object);
        $this->getDoctrine()->getManager()->flush();

        return $response;
    }
} 
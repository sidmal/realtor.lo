<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 05.05.14
 * Time: 0:48
 */

namespace Realtor\DictionaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/user/get/ajax", name="user_get_ajax")
     * @Method({"POST"})
     */
    public function getUserById(Request $request)
    {
        if(!$request->isXmlHttpRequest()){
            return new Response(null, 403);
        }

        if(!$request->request->has('user_id') || !$userId = $request->request->get('user_id')){
            return new Response(null, 403);
        }

        $userManager = $this->container->get('manager.user');

        $user = $userManager->loadUserById($userId);

        if(!$user){
            return new Response(null, 403);
        }

        $user[0]['app_id'] = $userManager->save($user[0]);

        $user[0]['head_phone'] = '';
        if($user[0]['id_manager'] > 0){
            $head = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')
                ->findOneBy(['outerId' => $user[0]['id_manager']]);

            if($head){
                $user[0]['head_phone'] = $head->getOfficePhone();
            }
        }

        return new Response(json_encode($user[0]));
    }
} 
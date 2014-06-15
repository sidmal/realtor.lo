<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 05.05.14
 * Time: 0:48
 */

namespace Realtor\DictionaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        /*if($this->container->get('kernel')->getEnvironment() == 'dev'){
            $userId = 233963;
        }*/

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

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/user/get/manager/by/branch/ajax", name="user_get_manager_by_branch_ajax")
     * @Method({"POST"})
     */
    public function getManagerByBranchAction(Request $request)
    {
        if(!$request->request->has('branch_id')){
            return (new Response())->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $managers = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')
            ->getManagerByBranch($request->request->get('branch_id'));

        if(!$managers){
            return (new Response())->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $response = [];
        foreach($managers as $manager){
            $response[] = [
                'id' => $manager->getId(),
                'name' => $manager->getFio().' ('.$manager->getUsername().')'
            ];
        }

        return new Response(json_encode($response));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/user/get/agent/by/manager/ajax", name="user_get_agent_by_manager_ajax")
     * @Method({"POST"})
     */
    public function getAgentByManagerAction(Request $request)
    {
        if(!$request->request->has('manager_id')){
            return (new Response())->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $agents = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')
            ->getAgentByManager($request->request->get('manager_id'));

        if(!$agents){
            return (new Response())->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $response = [];
        foreach($agents as $agent){
            $response[] = [
                'id' => $agent->getId(),
                'name' => $agent->getFio().' ('.$agent->getUsername().')'
            ];
        }

        return new Response(json_encode($response));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/user/get/by/name/ajax", name="user_get_by_name_ajax")
     * @Method({"GET"})
     */
    public function getUserByNameAction(Request $request)
    {
        $response = new JsonResponse();

        if(!$request->isXmlHttpRequest()){
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        if(!$request->query->has('term') || !$userName = $request->query->get('term')){
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $users = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:User')
            ->getUserByName($userName);

        $responseData = [];
        foreach($users as $user){
            $responseData[] = [
                'id' => $user->getId(),
                'branch_name' => $user->getBranch()->getName(),
                'name' => $user->getFio(),
                'phone' => $user->getPhone(),
                'office_phone' => $user->getOfficePhone(),
                'may_trans_to_cell_phone' => $user->getMayRedirectCall(),
                'manager_office_phone' => $user->getHead() ? $user->getHead()->getOfficePhone() : false,
                'branch_phone' => $user->getBranch()->getBranchPhone()
            ];
        }

        return $response->setData($responseData);
    }
} 
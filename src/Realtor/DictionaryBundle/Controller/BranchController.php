<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 05.05.14
 * Time: 3:35
 */

namespace Realtor\DictionaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BranchController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/branch/get/ajax", name="branch_get_ajax")
     * @Method({"POST"})
     */
    public function getBranchById(Request $request)
    {
        if(!$request->isXmlHttpRequest()){
            return new Response(null, 403);
        }

        if(!$request->request->has('branch_id') || !$branchId = $request->request->get('branch_id')){
            return new Response(null, 403);
        }

        $branch = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
            ->findOneBy(['outerId' => $branchId]);

        if(!$branch){
            return new Response(null, 403);
        }

        $response = [];

        if($branch->getOnDutyAgentPhone()){
            $response['branch_phone'] = $branch->getOnDutyAgentPhone();
        }
        elseif($branch->getBranchNumber()){
            $response['branch_phone'] = $branch->getBranchNumber();
        }
        elseif($branch->getCityPhone()){
            $response['branch_phone'] = $branch->getCityPhone();
        }
        else{
            return new Response(null, 403);
        }

        $response['name'] = $branch->getName();
        $response['address'] = $branch->getAddress();


        return new Response(json_encode($response));
    }

    /**
     * @Route("/branches/get/all/ajax", name="branches_get_all_ajax")
     * @Method({"POST"})
     */
    public function getBranchesAction(Request $request)
    {
        $response = new Response();

        if(!$request->isXmlHttpRequest()){
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $branches = $this->getDoctrine()->getManager()->getRepository('DictionaryBundle:Branches')
            ->findBy(['isActive' => true]);

        if(!$branches){
            return $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $result = [];
        foreach($branches as $branch){
            if($branch->getOnDutyAgentPhone()){
                $result[] = ['id' => $branch->getId(), 'duty_agent' => $branch->getOnDutyAgentPhone()];
            }
        }

        return $response->setContent(json_encode($result));
    }
} 
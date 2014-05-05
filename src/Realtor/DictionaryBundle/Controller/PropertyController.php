<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 03.05.14
 * Time: 0:35
 */

namespace Realtor\DictionaryBundle\Controller;

use Guzzle\Http\Exception\RequestException;
use Realtor\DictionaryBundle\Model\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PropertyController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/property/search/ajax", name="search_property_ajax")
     * @Method({"GET"})
     */
    public function getPropertyAjax(Request $request)
    {
        if(!$request->isXmlHttpRequest()){
            return new Response(null, 403);
        }

        $httpClient = (new HttpClient())->getClient();

        try{
            $response = $httpClient->get(
                'http://disp.emls.ru/api/property/begins_with/?q='.$request->query->get('term'),
                ['Accept' => 'application/json']
            )->send();

            return new Response(json_encode($response->json()));
        }
        catch(RequestException $e){
            echo $e->getMessage();
            return new Response(null, 404);
        }

        return new Response();
    }
} 
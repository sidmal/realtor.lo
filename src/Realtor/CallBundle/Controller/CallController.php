<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 24.04.14
 * Time: 17:15
 */

namespace Realtor\CallBundle\Controller;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CallController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/call/ajax", name="call_ajax")
     * @Method({"POST"})
     */
    public function ajaxCallAction(Request $request)
    {
        $to = $request->request->get('to_phone');

        $httpClient = new Client();
        try{
            $response = $httpClient->post('http://188.227.101.17:8080', [], ['cid' => '201', 'did' => $to])->send();

            $response = [
                'call_state' => true,
                'call_comment' => print_r($response->getStatusCode(), true)
            ];
        }
        catch(RequestException $e){
            $response = [
                'call_state' => false,
                'call_comment' => $e->getMessage()
            ];
        }

        return new Response(json_encode($response));
    }
} 
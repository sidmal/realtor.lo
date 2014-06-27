<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 26.06.14
 * Time: 21:54
 */

namespace Application\Sonata\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class DutyController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/duty/get/unfilled", name="duty_get_unfilled")
     * @Method({"GET"})
     */
    public function getUnfilledDutyAction(Request $request)
    {
        $response = new JsonResponse();

        if(!$request->isXmlHttpRequest()){
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $date = [
            [
                'date' => '2014-06-01',
                'badge' => false,
                'title' => 'Tonight',
                'body' => '<p class="lead">Party</p><p>Like it\'s 1999.</p>',
                'footer' => 'At Paisley Park',
                'classname' => 'grade-1'
            ],
            [
                'date' => '2014-06-26',
                'badge' => false,
                'title' => 'Tonight',
                'body' => '<p class="lead">Party</p><p>Like it\'s 1999.</p>',
                'footer' => 'At Paisley Park',
                'classname' => 'grade-1'
            ],
            [
                'date' => '2014-06-27',
                'badge' => false,
                'title' => 'Tonight',
                'body' => '<p class="lead">Party</p><p>Like it\'s 1999.</p>',
                'footer' => 'At Paisley Park',
                'classname' => 'grade-1'
            ],
            [
                'date' => '2014-06-28',
                'badge' => false,
                'title' => 'Tonight',
                'body' => '<p class="lead">Party</p><p>Like it\'s 1999.</p>',
                'footer' => 'At Paisley Park',
                'classname' => 'grade-1'
            ],
            [
                'date' => '2014-06-29',
                'badge' => false,
                'title' => 'Tonight',
                'body' => '<p class="lead">Party</p><p>Like it\'s 1999.</p>',
                'footer' => 'At Paisley Park',
                'classname' => 'grade-1'
            ]
        ];

        return $response->setData($date);
    }
} 
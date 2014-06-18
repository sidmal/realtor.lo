<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 23.05.14
 * Time: 22:27
 */

namespace Application\Sonata\UserBundle\Controller;

use Realtor\AdminBundle\Traits\Security;
use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DutyCRUDController extends CRUDController
{
    /**
     * @Route("/print", name="print")
     */
    public function printAction()
    {
        $user = $this->getUser();
        $redirectResponse = new RedirectResponse($this->generateUrl('admin_sonata_user_duty_list'));

        $dutyRepository = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:Duty');

        $type = $this->get('request')->get('type');
        if($type > 2 || $type < 0){
            $type = 1;
        }

        if($user->isManager()){
            $dutyList = $dutyRepository->getDuty($type, $this->getUser()->getId());
        }
        elseif($user->isDirector() || $user->isAdministrator()){
            $dutyList = $dutyRepository->getDuty($type);
        }
        else{
            if(!$this->get('request')->isXmlHttpRequest()){
                return $redirectResponse;
            }
            else{
                return (new Response())->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
        }

        if(!$dutyList){
            if(!$this->get('request')->isXmlHttpRequest()){
                return $redirectResponse;
            }
            else{
                return (new Response())->setStatusCode(Response::HTTP_NOT_FOUND);
            }
        }

        if($this->get('request')->isXmlHttpRequest()){
            return new Response();
        }

        $dutyMonth = new \DateTime();

        if($type == 0){
            $dutyMonth->modify('last day of -1 month');
        }
        elseif($type == 2){
            $dutyMonth->modify('last day of +1 month');
        }

        return $this->render(
            '@ApplicationSonataUser/CRUD/print.html.twig',
            [
                'duty' => $dutyList,
                'duty_month' => mb_strtoupper($dutyRepository->getMonthRus($dutyMonth->format('n')), 'utf-8').' '.$dutyMonth->format('Y'),
                'duty_min_hour' => $this->container->getParameter('duty.min.hour'),
                'duty_max_hour' => $this->container->getParameter('duty.max.hour')
            ]
        );
    }
} 
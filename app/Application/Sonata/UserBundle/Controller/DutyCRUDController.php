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

class DutyCRUDController extends CRUDController
{
    /**
     * @Route("/print", name="print")
     */
    public function printAction()
    {
        $securityContext = $this->container->get('security.context');
        $redirectResponse = new RedirectResponse($this->generateUrl('admin_sonata_user_duty_list'));

        $dutyRepository = $this->getDoctrine()->getManager()->getRepository('ApplicationSonataUserBundle:Duty');

        if($securityContext->isGranted('ROLE_APP_MANAGER')){
            $dutyList = $dutyRepository->getDuty($this->getUser()->getId());
        }
        elseif($securityContext->isGranted('ROLE_APP_OFFICE_DIRECTOR') || $securityContext->isGranted('ROLE_APP_ADMINISTRATOR')){
            $dutyList = $dutyRepository->getDuty();
        }
        else{
            return $redirectResponse;
        }

        if(!$dutyList){
            return $redirectResponse;
        }

        $dutyMonth = new \DateTime();

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
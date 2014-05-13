<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 05.05.14
 * Time: 23:51
 */

namespace Application\Sonata\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class RedirectController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse
     *
     * @Route("/", name="redirect_index")
     * @Method({"POST", "GET"})
     */
    public function indexAction(Request  $request)
    {
        return new RedirectResponse($this->generateUrl('sonata_admin_dashboard'));
    }

    /**
     * @return RedirectResponse
     *
     * @Route("/login", name="fos_user_security_login")
     */
    public function loginAction()
    {
        return new RedirectResponse($this->generateUrl('sonata_user_admin_security_login'));
    }
} 
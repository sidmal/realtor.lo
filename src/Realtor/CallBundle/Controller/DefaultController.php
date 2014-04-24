<?php

namespace Realtor\CallBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CallBundle:Default:index.html.twig', array('name' => $name));
    }
}

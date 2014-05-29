<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 29.05.14
 * Time: 16:35
 */

namespace Realtor\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

class AdminController extends CRUDController
{
    public function listAction()
    {
        return parent::listAction();
    }

    public function deleteAction($id)
    {

    }

    public function editAction($id = null)
    {
        $securityContext = $this->container->get('security.context');
        if(!$securityContext->isGranted('ROLE_APP_ADMINISTRATOR')){
            throw new AccessDeniedException(sprintf('Admin ID %s has no access to product with id', $id));
        }

        return parent::editAction($id);
    }

    public function createAction()
    {

    }

    public function showAction($id = null)
    {

    }
} 
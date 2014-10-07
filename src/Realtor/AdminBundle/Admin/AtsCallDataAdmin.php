<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 02.10.14
 * Time: 23:30
 */

namespace Realtor\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AtsCallDataAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')->remove('edit')->remove('export')->remove('delete');
        $collection
            ->add('get_ats_calls_report_data', 'report');
    }

    protected function configureListFields(ListMapper $listMapper)
    {

    }
} 
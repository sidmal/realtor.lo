<?php

namespace Realtor\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class BranchesAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('outerId')
            ->add('branchNumber')
            ->add('name')
            ->add('address')
            ->add('cityPhone')
            ->add('onDutyAgentPhone')
            ->add('isActive')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('branchNumber')
            ->add('name')
            ->add('address')
            ->add('cityPhone')
            ->add('onDutyAgentPhone')
            ->add('isActive')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => ['template' => 'AdminBundle:Default:edit.html.twig'],
                    'Позвонить' => ['template' => 'AdminBundle:Default:call_ajax.html.twig']
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('outerId')
            ->add('branchNumber')
            ->add('name')
            ->add('address')
            ->add('cityPhone')
            ->add('onDutyAgentPhone')
            ->add('isActive')
            ->add('createdAt')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('outerId')
            ->add('branchNumber')
            ->add('name')
            ->add('address')
            ->add('cityPhone')
            ->add('onDutyAgentPhone')
            ->add('isActive')
            ->add('createdAt')
        ;
    }
}

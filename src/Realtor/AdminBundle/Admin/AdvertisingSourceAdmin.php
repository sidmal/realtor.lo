<?php

namespace Realtor\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AdvertisingSourceAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id',  null, ['label' => 'Идентификатор'])
            ->add('name', null, ['label' => 'Наименование'])
            ->add('isActive', null, ['label' => 'Активен'])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', 'integer', ['label' => 'Идентификатор'])
            ->add('name', 'string', ['label' => 'Наименование'])
            ->add('isActive', 'boolean', ['label' => 'Активен'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'Наименование', 'required' => true])
            ->add('isActive', 'checkbox', ['label' => 'Активен', 'required' => false])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id', null, ['label' => 'Идентификатор'])
            ->add('outerId', null, ['label' => 'Идентификатор emls'])
            ->add('name', null, ['label' => 'Наименование'])
            ->add('isActive', null, ['label' => 'Активен'])
            ->add('createdAt', null, ['label' => 'Дата создания'])
        ;
    }
}

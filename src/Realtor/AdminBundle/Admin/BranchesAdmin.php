<?php

namespace Realtor\AdminBundle\Admin;

use Realtor\AdminBundle\Traits\Security;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class BranchesAdmin extends Admin
{
    use Security;

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('branchNumber', null, ['label' => 'Внутренний телефонный номер'])
            ->add(
                'name',
                'doctrine_orm_callback',
                [
                    'label' => 'Наименование',
                    'callback' => function($builder, $alias, $field, $value){
                        if(!$value) return;

                        $builder->andWhere($builder->expr()->like($builder->expr()->lower($alias.'.name'), $builder->expr()->lower(':name')))
                            ->setParameter('name', '%'.preg_replace('/\ {2,}/', ' ', $value['value']).'%');

                        return true;
                    }
                ]
            )
            ->add(
                'address',
                'doctrine_orm_callback',
                [
                    'label' => 'Адрес',
                    'callback' => function($builder, $alias, $field, $value){
                        if(!$value) return;

                        $builder->andWhere($builder->expr()->like($builder->expr()->lower($alias.'.address'), $builder->expr()->lower(':address')))
                            ->setParameter('address', '%'.preg_replace('/\ {2,}/', ' ', $value['value']).'%');

                        return true;
                    }
                ]
            )
            ->add('cityPhone', null, ['label' => 'Городской телефонный номер'])
            ->add('onDutyAgentPhone', null, ['label' => 'Номер дежурного'])
            ->add('isActive', null, ['label' => 'Активен'])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'Идентификатор филиала'])
            ->add('name', null, ['label' => 'Наименование'])
            ->add('address', null, ['label' => 'Адрес'])
            ->add('isActive', null, ['label' => 'Активен'])
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => ['template' => 'AdminBundle:Default:show.html.twig'],
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
            ->add('name', null, ['label' => 'Наименование', 'required' => true])
            ->add('branchNumber', null, ['label' => 'Внутренний телефонный номер', 'required' => false])
            ->add('address', null, ['label' => 'Адрес', 'required' => false])
            ->add('cityPhone', null, ['label' => 'Городской телефонный номер', 'required' => false])
            ->add('onDutyAgentPhone', null, ['label' => 'Номер дежурного', 'required' => false])
            ->add('isActive', null, ['label' => 'Активен', 'required' => false])
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
            ->add('branchNumber', null, ['label' => 'Внутренний телефонный номер'])
            ->add('name', null, ['label' => 'Наименование'])
            ->add('address', null, ['label' => 'Адрес'])
            ->add('cityPhone', null, ['label' => 'Городской телефонный номер'])
            ->add('onDutyAgentPhone', null, ['label' => 'Номер дежурного'])
            ->add('isActive', null, ['label' => 'Активен'])
        ;
    }
}

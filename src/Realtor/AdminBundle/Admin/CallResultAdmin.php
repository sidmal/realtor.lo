<?php

namespace Realtor\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CallResultAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, ['label' => 'Идентификатор'])
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
            ->add('isActive', null, ['label' => 'Активен'])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'Идентификатор'])
            ->add('name', null, ['label' => 'Наименование'])
            ->add('isActive', null, ['label' => 'Активен'])
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => ['template' => 'AdminBundle:Default:show.html.twig'],
                    'edit' => ['template' => 'AdminBundle:Default:edit.html.twig'],
                    'delete' => ['template' => 'AdminBundle:Default:delete.html.twig'],
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
            ->add('name', null, ['label' => 'Наименование'])
            ->add('isActive', null, ['label' => 'Активен'])
            ->add('createdAt', null, ['label' => 'Дата заведения'])
        ;
    }
}

<?php

namespace Realtor\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class BlackListAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('phone', null, ['label' => 'Номер телефона'])
            ->add(
                'reason',
                'doctrine_orm_callback',
                [
                    'label' => 'Причина',
                    'callback' => function($builder, $alias, $field, $value){
                        if(!$value) return;

                        $builder->andWhere($builder->expr()->like($builder->expr()->lower($alias.'.reason'), $builder->expr()->lower(':reason')))
                            ->setParameter('reason', '%'.preg_replace('/\ {2,}/', ' ', $value['value']).'%');

                        return true;
                    }
                ]
            );
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, ['label' => 'Идентификатор'])
            ->add('phone', null, ['label' => 'Номер телефона'])
            ->add('reason', null, ['label' => 'Причина'])
            ->add('createdAt', null, ['label' => 'Дата добавления'])
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'AdminBundle:Default:show.html.twig'],
                        'delete' => ['template' => 'AdminBundle:Default:delete.html.twig'],
                    ]
                ]
            )
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id', null, ['label' => 'Идентификатор'])
            ->add('phone', null, ['label' => 'Номер телефона'])
            ->add('reason', null, ['label' => 'Причина'])
            ->add('createdAt', null, ['label' => 'Дата добавления'])
        ;
    }
}

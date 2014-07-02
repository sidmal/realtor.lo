<?php

namespace Realtor\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class UserPhonesAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->add('verify_user_phone', $this->getRouterIdParameter().'/verify');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('appendedUserId', null, ['label' => 'Добавил'])
            ->add('phone', null, ['label' => 'Номер телефона'])
            ->add('isVerify', null, ['label' => 'Верифицирован'])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'Идентификатор'])
            ->add('phone', null, ['label' => 'Номер телефона'])
            ->add('phoneBeforeReplace', null, ['label' => 'Заменяемый номер телефона'])
            ->add('userId', null, ['label' => 'Чей номер'])
            ->add('appendedUserId', null, ['label' => 'Добавил'])
            ->add('isVerify', null, ['label' => 'Верифицирован'])
            ->add('createdAt', null, ['label' => 'Дата добавления'])
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'AdminBundle:Default:show.html.twig'],
                        'delete' => ['template' => 'AdminBundle:Default:delete.html.twig'],
                        'verify' => ['template' => 'AdminBundle:Default:verify_user_phone.html.twig'],
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
            ->add('userId', null, ['label' => 'Чей номер'])
            ->add('appendedUserId', null, ['label' => 'Добавил'])
            ->add('isVerify', null, ['label' => 'Верифицирован'])
            ->add('createdAt', null, ['label' => 'Дата добавления'])
        ;
    }
}

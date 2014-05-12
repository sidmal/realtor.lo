<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 10.05.14
 * Time: 23:04
 */

namespace Realtor\AdminBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;

class UserAdmin extends SonataUserAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete')->remove('create');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('outerId', null, ['label' => 'Идентификатор сотрудника emls', 'required' => true])
                ->add('username', 'text', ['label' => 'Логин сотрудника', 'required' => true])
                ->add('fio', 'text', ['label' => 'ФИО', 'required' => true])
                ->add('firstName', 'text', ['label' => 'Имя', 'required' => true])
                ->add('secondName', 'text', ['label' => 'Отчество', 'required' => true])
                ->add('lastName', 'text', ['label' => 'Фамилия', 'required' => true])
                ->add('email')
                ->add('branch', null, ['label' => 'Приписан к филиалу', 'required' => true])
                ->add('inBranch', null, ['label' => 'Находится в филиале'])
                ->add('head', null, ['label' => 'Руководитель'])
                ->add('isFired', null, ['label' => 'Сотрудник уволен'])
                ->add('firedAt', null, ['label' => 'Дата увольнения'])
                ->add('inOffice', null, ['label' => 'Сотрудник в офисе', 'required' => true])
                ->add('officePhone', null, ['label' => 'Рабочий телефон', 'required' => true])
                ->add('phone', null, ['label' => 'Личные телефоны', 'required' => true])
                ->add('mayRedirectCall', null, ['label' => 'Разрешен первод звонка на личный телефон', 'required' => true])
            ->end()
            ->with('Groups')
                ->add('groups', 'sonata_type_model', array(
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true
                ))
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('outerId', null, ['label' => 'Идентификатор сотрудника emls'])
                ->add('username', 'text', ['label' => 'Логин сотрудника'])
                ->add('fio', 'text', ['label' => 'ФИО'])
                ->add('firstName', 'text', ['label' => 'Имя'])
                ->add('secondName', 'text', ['label' => 'Отчество'])
                ->add('lastName', 'text', ['label' => 'Фамилия'])
                ->add('email')
                ->add('branch', null, ['label' => 'Приписан к филиалу'])
                ->add('inBranch', null, ['label' => 'Находится в филиале'])
                ->add('head', null, ['label' => 'Руководитель'])
                ->add('isFired', null, ['label' => 'Сотрудник уволен'])
                ->add('firedAt', null, ['label' => 'Дата увольнения'])
                ->add('inOffice', null, ['label' => 'Сотрудник в офисе'])
                ->add('officePhone', null, ['label' => 'Рабочий телефон'])
                ->add('phone', null, ['label' => 'Личные телефоны'])
                ->add('mayRedirectCall', null, ['label' => 'Разрешен перевод звонка на личный телефон'])
                ->end()
            ->with('Groups')
                ->add('groups')
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('fio', 'text', ['label' => 'ФИО'])
            ->add('firstName', 'text', ['label' => 'Имя'])
            ->add('lastName', 'text', ['label' => 'Фамилия'])
            ->add('officePhone', null, ['label' => 'Рабочий телефон'])
            ->add('email')
            ->add('groups')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add(
                'username',
                'doctrine_orm_callback',
                [
                    'label' => 'Логин',
                    'callback' => function($builder, $alias, $field, $value){
                        if(!$value) return;

                        $builder->andWhere($builder->expr()->like($builder->expr()->lower($alias.'.username'), $builder->expr()->lower(':username')))
                            ->setParameter('username', '%'.preg_replace('/\ {2,}/', ' ', $value['value']).'%');

                        return true;
                    }
                ]
            )
            ->add(
                'email',
                'doctrine_orm_callback',
                [
                    'callback' => function($builder, $alias, $field, $value){
                        if(!$value) return;

                        $builder->andWhere($builder->expr()->like($builder->expr()->lower($alias.'.email'), $builder->expr()->lower(':email')))
                            ->setParameter('email', '%'.preg_replace('/\ {2,}/', ' ', $value['value']).'%');

                        return true;
                    }
                ]
            )
            ->add(
                'firstName',
                'doctrine_orm_callback',
                [
                    'label' => 'Имя',
                    'callback' => function($builder, $alias, $field, $value){
                        if(!$value) return;

                        $builder->andWhere($builder->expr()->like($builder->expr()->lower($alias.'.firstName'), $builder->expr()->lower(':firstName')))
                            ->setParameter('firstName', '%'.preg_replace('/\ {2,}/', ' ', $value['value']).'%');

                        return true;
                    }
                ]
            )
            ->add(
                'lastName',
                'doctrine_orm_callback',
                [
                    'label' => 'Фамилия',
                    'callback' => function($builder, $alias, $field, $value){
                        if(!$value) return;

                        $builder->andWhere($builder->expr()->like($builder->expr()->lower($alias.'.lastName'), $builder->expr()->lower(':lastName')))
                            ->setParameter('lastName', '%'.preg_replace('/\ {2,}/', ' ', $value['value']).'%');

                        return true;
                    }
                ]
            )
            ->add('groups', null, ['label' => 'Группа'])
        ;
    }
} 
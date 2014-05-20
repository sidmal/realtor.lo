<?php

namespace Realtor\AdminBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Parameter;
use Realtor\AdminBundle\Traits\Security;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class DutyAdmin extends Admin
{
    use Security;

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $em =  $this->modelManager->getEntityManager('ApplicationSonataUserBundle:User');

        $builder = $em->createQueryBuilder()
            ->select(
                [
                    'user.id',
                    'user.fio'
                ]
            )
            ->from('ApplicationSonataUserBundle:User', 'user')
            ->where('user.head = :head')
            ->andWhere('user.isFired = :isFired')
            ->setParameters(
                new ArrayCollection(
                    [
                        new Parameter('head', $this->getUser()->getId()),
                        new Parameter('isFired', false)
                    ]
                )
            );

        $employees = [];

        try{
            $users = $builder->getQuery()->getArrayResult();

            foreach($users as $user){
                $employees[$user['id']] = $user['fio'];
            }
        }
        catch(NoResultException $e){

        }

        $datagridMapper
            ->add(
                'userId',
                'doctrine_orm_string',
                ['label' => 'Дежурный'],
                'choice',
                [
                    'choices' => $employees
                ]
            )
            ->add('branchId', null, ['label' => 'Дежурит в филиале'])
            ->add('phone', null, ['label' => 'Номер телефона дежурного'])
            ->add(
                'dutyStartAt',
                'doctrine_orm_datetime',
                ['label' => 'Дата начала дежурства'],
                null,
                [
                    'widget' => 'single_text'
                ]
            )
            ->add(
                'dutyEndAt',
                'doctrine_orm_datetime',
                ['label' => 'Дата окончания дежурства'],
                null,
                [
                    'widget' => 'single_text'
                ]
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('dutyStartAt')
            ->add('dutyEndAt')
            ->add('phone')
            ->add('createAt')
            ->add('updatedAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $em =  $this->modelManager->getEntityManager('ApplicationSonataUserBundle:User');

        $query = $em->createQueryBuilder()
            ->select('user')
            ->from('ApplicationSonataUserBundle:User', 'user')
            ->where('user.head = :head')
            ->andWhere('user.isFired = :isFired')
            ->setParameters(
                new ArrayCollection(
                    [
                        new Parameter('head', $this->getUser()->getId()),
                        new Parameter('isFired', false)
                    ]
                )
            );

        $formMapper
            ->add(
                'branchId',
                null,
                [
                    'label' => 'Дежурит в филиале',
                    'required' => true,
                    'empty_value' => 'Выбирите филиал дежурства',
                ]
            )
            ->add(
                'manager',
                'sonata_type_model',
                [
                    'label' => 'Дежурный менеджер',
                    'required' => true,
                    'empty_value' => 'Выбирите дежурного менеджера',
                    'choices' => []
                ]
            )
            ->add(
                'userId',
                'sonata_type_model',
                [
                    'label' => 'Дежурный агент',
                    'choices' => [],
                    'required' => true,
                    'empty_value' => 'Выбирите дежурного агента'
                ]
            )
            ->add('phone', null, ['label' => 'Номер дежурного'])
            ->add(
                'dutyStartAt',
                'datetime',
                [
                    'label' => 'Дата начала дежурства',
                    'data' => new \DateTime(),
                    'with_minutes' => false
                ]
            )
            ->add(
                'dutyEndAt',
                'datetime',
                [
                    'label' => 'Дата окончания дежурства',
                    'data' => (new \DateTime())->add(new \DateInterval('PT3H')),
                    'with_minutes' => false
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
            ->add('id')
            ->add('dutyStartAt')
            ->add('dutyEndAt')
            ->add('phone')
            ->add('createAt')
            ->add('updatedAt')
        ;
    }
}

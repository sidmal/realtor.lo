<?php

namespace Realtor\AdminBundle\Admin;

use Realtor\AdminBundle\Traits\Security;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\HttpFoundation\ParameterBag;

class DutyAdmin extends Admin
{
    use Security;

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('print');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    /*protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'branchId',
                null,
                [
                    'label' => 'Дежурит в филиале'
                ],
                null,
                [
                    'empty_value' => 'Выберите филиал дежурства',
                ]
            )
            ->add(
                'userId',
                null,
                [
                    'label' => 'Дежурный агент'
                ],
                null,
                [
                    'empty_value' => 'Выберите дежурного агента'
                ]
            )
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
    }*/

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'Идентификатор'])
            ->add('branchId', null, ['label' => 'Дежурит в филиале'])
            ->add('dutyAgent', null, ['label' => 'Дежурный агент'])
            ->add('dutyPhone', null, ['label' => 'Номер телефона дежурного'])
            ->add('dutyDate', null, ['label' => 'Дата начала дежурства'])
            ->add('dutyTime', null, ['label' => 'Дата окончания дежурства'])
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'AdminBundle:Default:show.html.twig'],
                        'edit' => ['template' => 'AdminBundle:Default:edit.html.twig'],
                        'delete' => ['template' => 'AdminBundle:Default:delete.html.twig'],
                    ]
                ]
            )
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $managers = $agent = [];
        try{
            if($this->getRequest()->request->all()){
                $request = new ParameterBag(current($this->getRequest()->request->all()));
            }

            if(isset($request)){
                if($request->has('branchId')){
                    $managers = $this->getConfigurationPool()->getContainer()->get('doctrine')
                        ->getManager()->getRepository('ApplicationSonataUserBundle:User')
                        ->getManagerByBranch($request->get('branchId'));
                }

                if($request->has('manager')){
                    $agent = $this->getConfigurationPool()->getContainer()->get('doctrine')
                        ->getManager()->getRepository('ApplicationSonataUserBundle:User')
                        ->getAgentByManager($request->get('manager'));
                }
            }

            if($this->getSubject()->getId()){
                $managers = $this->getConfigurationPool()->getContainer()->get('doctrine')
                    ->getManager()->getRepository('ApplicationSonataUserBundle:User')
                    ->getManagerByBranch($this->getSubject()->getBranchId());

                $agent = $this->getConfigurationPool()->getContainer()->get('doctrine')
                    ->getManager()->getRepository('ApplicationSonataUserBundle:User')
                    ->getAgentByManager($this->getSubject()->getManager());
            }
        }
        catch(\Exception $e){

        }

        $hours = [];
        for($index = $this->getConfigurationPool()->getContainer()->getParameter('duty.min.hour'); $index <= $this->getConfigurationPool()->getContainer()->getParameter('duty.max.hour'); $index++){
            $hours[] = $index;
        }

        if(!$this->getSubject()->getId()){
            $dutyStartDate = new \DateTime();
            $dutyStartDate->modify('+1 month');

            if($dutyStartDate->format('H') < $this->getConfigurationPool()->getContainer()->getParameter('duty.min.hour')
                || $dutyStartDate->format('H') > $this->getConfigurationPool()->getContainer()->getParameter('duty.max.hour')
                || ($dutyStartDate->format('H') + $this->getConfigurationPool()->getContainer()->getParameter('duty.delta.hour')) >= $this->getConfigurationPool()->getContainer()->getParameter('duty.max.hour')){
                $dutyStartDate->setTime($this->getConfigurationPool()->getContainer()->getParameter('duty.min.hour'), 0);
            }

            $dutyEndDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dutyStartDate->format('Y-m-d').' '.($dutyStartDate->format('H') + $this->getConfigurationPool()->getContainer()->getParameter('duty.delta.hour')).':00:00');
        }
        else{
            $dutyStartDate = $this->getSubject()->getDutyStartAt();
            $dutyEndDate = $this->getSubject()->getDutyEndAt();
        }

        $formMapper
            ->add(
                'branchId',
                null,
                [
                    'label' => 'Дежурит в филиале',
                    'required' => true,
                    'empty_value' => 'Выберите филиал дежурства'
                ]
            )
            ->add(
                'manager',
                null,
                [
                    'label' => 'Дежурный менеджер',
                    'required' => true,
                    'empty_value' => 'Выберите дежурного менеджера',
                    'class' => 'ApplicationSonataUserBundle:User',
                    'choices' => $managers
                ]
            )
            ->add(
                'userId',
                'entity',
                [
                    'label' => 'Дежурный агент',
                    'required' => true,
                    'empty_value' => 'Выберите дежурного агента',
                    'class' => 'ApplicationSonataUserBundle:User',
                    'choices' => $agent,
                ]
            )
            ->add(
                'phone',
                null,
                [
                    'label' => 'Номер дежурного',
                    'attr' => ['readonly' => true]
                ]
            )
            ->add(
                'dutyStartAt',
                'datetime',
                [
                    'label' => 'Дата начала дежурства',
                    'data' => $dutyStartDate,
                    'with_minutes' => false,
                    'hours' => $hours
                ]
            )
            ->add(
                'dutyEndAt',
                'datetime',
                [
                    'label' => 'Дата окончания дежурства',
                    'data' => $dutyEndDate,
                    'with_minutes' => false,
                    'hours' => $hours
                ]
            )
            ->add(
                'duty_delta_hour',
                'hidden',
                [
                    'mapped' => false,
                    'data' => $this->getConfigurationPool()->getContainer()->getParameter('duty.delta.hour'),
                    'attr' => [
                        'hidden' => true
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
            ->add('branchId', null, ['label' => 'Дежурит в филиале'])
            ->add('manager', null, ['label' => 'Дежурный менеджер'])
            ->add('userId', null, ['label' => 'Дежурный агент'])
            ->add('phone', null, ['label' => 'Номер телефона дежурного'])
            ->add('dutyStartAt', null, ['label' => 'Дата начала дежурства'])
            ->add('dutyEndAt', null, ['label' => 'Дата окончания дежурства'])
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $dutyCheck = $this->getConfigurationPool()->getContainer()->get('doctrine')
            ->getManager()->getRepository('ApplicationSonataUserBundle:Duty')
            ->checkDuty($this->getSubject()->getBranchId(), $this->getSubject()->getDutyStartAt(), $this->getSubject()->getDutyEndAt());

        if($dutyCheck){
            $errorElement
                ->with('branchId')
                ->addViolation('Для указанного филиала уже назначен дежурный на данный временной период.')
                ->end();
        }
    }
}

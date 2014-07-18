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
            ->add('manager', null, ['label' => 'Дежурный менеджер', 'mapped' => false])
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
        $formMapper
            ->add('branchId', null, ['label' => 'Дежурит в филиале'])
            ->add('manager', null, ['label' => 'Дежурный менеджер', 'mapped' => false])
            ->add('dutyAgent', null, ['label' => 'Дежурный агент'])
            ->add('dutyPhone', null, ['label' => 'Номер телефона дежурного'])
            ->add('dutyDate', null, ['label' => 'Дата начала дежурства'])
            ->add('dutyTime', null, ['label' => 'Дата окончания дежурства'])
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

<?php

namespace Application\Sonata\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DutyInBranchesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dutyDate')
            ->add('dutyTime')
            ->add('dutyPhone')
            ->add(
                'branchId',
                'entity',
                [
                    'class' => 'Realtor\DictionaryBundle\Entity\Branches',
                    'property' => 'id'
                ]
            )
            ->add(
                'dutyAgent',
                'entity',
                [
                    'class' => 'Application\Sonata\UserBundle\Entity\User',
                    'property' => 'id'
                ]
            )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\UserBundle\Entity\DutyInBranches',
            'csrf_protection' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application_sonata_userbundle_dutyinbranches';
    }
}

<?php

namespace Application\Sonata\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('usernameCanonical')
            ->add('email')
            ->add('emailCanonical')
            ->add('enabled')
            ->add('salt')
            ->add('password')
            ->add('lastLogin')
            ->add('locked')
            ->add('expired')
            ->add('expiresAt')
            ->add('confirmationToken')
            ->add('passwordRequestedAt')
            ->add('roles')
            ->add('credentialsExpired')
            ->add('credentialsExpireAt')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('dateOfBirth')
            ->add('firstname')
            ->add('lastname')
            ->add('website')
            ->add('biography')
            ->add('gender')
            ->add('locale')
            ->add('timezone')
            ->add('phone')
            ->add('facebookUid')
            ->add('facebookName')
            ->add('facebookData')
            ->add('twitterUid')
            ->add('twitterName')
            ->add('twitterData')
            ->add('gplusUid')
            ->add('gplusName')
            ->add('gplusData')
            ->add('token')
            ->add('twoStepVerificationCode')
            ->add('outerId')
            ->add('isFired')
            ->add('firedAt')
            ->add('inOffice')
            ->add('officePhone')
            ->add('mayRedirectCall')
            ->add('secondName')
            ->add('fio')
            ->add('firstName')
            ->add('lastName')
            ->add('userDutyPhone')
            ->add('branch')
            ->add('inBranch')
            ->add('head')
            ->add('groups')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\UserBundle\Entity\User',
            'validation_groups' => ['Registration']
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application_sonata_userbundle_user';
    }
}

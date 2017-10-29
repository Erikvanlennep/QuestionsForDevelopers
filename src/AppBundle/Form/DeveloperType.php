<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class DeveloperType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array(
                'label' => 'profile.form.name',
                'attr' => array('style' => 'width:200px', 'placeholder' => 'profile.form.first_name')
            ))
            ->add('infix', TextType::class, array(
                'label' => 'profile.form.infix',
                'required' => false,
                'attr' => array('style' => 'width:120px', 'placeholder' => 'profile.form.infix')
            ))
            ->add('lastname', TextType::class, array(
                'label' => 'profile.form.surname',
                'attr' => array('style' => 'width:200px', 'placeholder' => 'profile.form.last_name')
            ))
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr' => array('style' => 'width:300px', 'placeholder' => 'e.g.@example.com')))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle','attr' => array('style' => 'width:300px')),
                'required' => true,
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
        ->remove('username');
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}

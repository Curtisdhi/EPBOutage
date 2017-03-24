<?php

namespace EPBOutage\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Type;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Type\TextType::class)
            ->add('email', Type\EmailType::class)
            ->add('message', Type\TextareaType::class, array(
                'attr' => array(
                    'minlength' => 10,
            )))
            ->add('recaptcha', EWZRecaptchaType::class)
            ->add('submit', Type\SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn-primary',
                )
            ));
    }

    public function getName()
    {
        return 'contactForm';
    }
}
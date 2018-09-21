<?php

namespace EPBOutage\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Type;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class AlertUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', Type\EmailType::class)
            ->add('customersAffectedThreshold', Type\NumberType::class, array(
                'attr' => array(
                    'min' => 100,
            )))
            ->add('recaptcha', EWZRecaptchaType::class);
    }

    public function getName()
    {
        return 'alertUserForm';
    }
}
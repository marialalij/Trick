<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'required' => true,
                'mapped' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'required' => true,
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'New password',
                ],
                'second_options' => [
                    'label' => 'Please confirm new password',
                ],
                'mapped' => false,
                'constraints' => [
                    new Regex(
                        [
                            'pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)^',
                            'message' => 'password must contain at least a lowercase, an uppercase, a number and a special character',
                        ]
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

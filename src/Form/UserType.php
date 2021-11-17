<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\Image;

class UserType extends AbstractType
{
    protected $auth;

    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userName', null, [
                'required' => true,
                'label' => false,
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => false,
            ])
            ->add('avatar', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image(),
                ],
            ])
            ->add('firstName', TextType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
                'label' => false,
            ]);

        if ($this->auth->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'User' => 'ROLE_USER',
                        'Admin' => 'ROLE_ADMIN',
                    ],
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => [
                        'class' => 'd-flex justify-content-between',
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

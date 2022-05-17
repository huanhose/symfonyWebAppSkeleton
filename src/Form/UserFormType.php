<?php

namespace App\Form;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

/**
 * Class that represent a User Profile Form
 */
class UserFormType extends AbstractType
{
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //Email can't be modified directly, is an action appart
            ->add('email', TextType::class, [
                'disabled' => true
            ])
            ->add('name')
            ->add('fullName')
            ->add('listRoles', ChoiceType::class, [
                'mapped'    => false,
                'required'  => false,
                'expanded'  => true,
                'multiple'  => true,
                'disabled'  => ! $options['canAssignRoles'],
                'label'     => 'List roles',
                'choices' => [
                        'Admin user'    => 'ROLE_ADMIN',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'canAssignRoles' => false
        ]);
    }
}

<?php
namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * A Create user form is very similar to Register User form
 * We don't use "agreeTerms"
 */
class CreateUserFormType extends RegistrationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->remove('agreeTerms');
    }
}
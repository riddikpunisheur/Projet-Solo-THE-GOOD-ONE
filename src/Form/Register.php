<?php

namespace App\Form;

use App\Entity\User;
use PhpParser\Builder\Class_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstname', null,[
            'contraints' => new NotBlank,
        ])
        ->add('lastname', null,[
            'contraints' => new NotBlank,
        ])
        ->add('email', EmailType::class,)
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
                'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.',
                    ]),
                ],
            
    }
}


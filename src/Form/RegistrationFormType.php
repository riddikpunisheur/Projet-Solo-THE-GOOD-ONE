<?php

namespace App\Form;

use App\Entity\User;
use \Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('first_name', null,[
            'contraints' => new NotBlank,
        ])
        ->add('last_name', null,[
            'contraints' => new NotBlank,
        ])
        ->add('email', EmailType::class,)
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
                'constraints' => [
                new IsTrue,([
                    'message' => 'You should agree to our terms.',
                ]),
            ],
        ])
        ->add('birthdate', BirthdayType::class, [
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                ])
        ->add('password', PasswordType::class, [
                'mapped' => false,
                'contraints' => new NotBlank([
                    'message' => 'Please enter a password'
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),           
        
        ]);        
          
    }
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => User::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
        }
}


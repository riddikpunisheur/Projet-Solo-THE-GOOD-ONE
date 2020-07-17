<?php

namespace App\Form;

use App\Entity\Questions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label')
            ->add('type')
            ->add('details')
            ->add('created_at')
            ->add('updated_at')
        ->add('user', null, [
            'expanded' => true,
            'multiple' => true,
        ])
        ->add('published', CheckboxType::class, [
            'label_attr' => ['class' => 'switch-custom'],
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Questions::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }

}

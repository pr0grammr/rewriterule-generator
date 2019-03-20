<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('csv_file', FileType::class, ['label' => 'CSV File'])
            ->add('options', ChoiceType::class, [
                'choices' => [
                    '301 Permanently moved' => 301,
                    'Custom Statuscode' => 'custom_code'
                ],
                'expanded' => true,
                'required' => false,
                'placeholder' => false
            ])
            ->add('custom_status_code', TextType::class, ['label' => false, 'required' => false])
            ->add('additional_flags', TextType::class, ['label' => 'Additional flags (like L, QSA)', 'required' => false])
            ->add('rewrite_engine', CheckboxType::class, ['label' => 'Include Rewrite Engine', 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}

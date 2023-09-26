<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Tutoring;
use App\Model\BatchTutoringSessionCreationModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BatchSessionCreationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tutoring', EntityType::class, [
                'class' => Tutoring::class,
            ])
            ->add('mondaySelected', CheckboxType::class, [
                'false_values' => ['false'],
                'data' => true,
            ])
            ->add('tuesdaySelected', CheckboxType::class, [
                'false_values' => ['false'],
                'data' => true,
            ])
            ->add('wednesdaySelected', CheckboxType::class, [
                'false_values' => ['false'],
                'data' => true,
            ])
            ->add('thursdaySelected', CheckboxType::class, [
                'false_values' => ['false'],
                'data' => true,
            ])
            ->add('fridaySelected', CheckboxType::class, [
                'false_values' => ['false'],
                'data' => true,
            ])
            ->add('startTime', TimeType::class, [
                'input' => 'datetime',
                'widget' => 'choice',
            ])
            ->add('endTime', TimeType::class, [
                'input' => 'datetime',
                'widget' => 'choice',
                'input_format' => 'H:i',
            ])
            ->add('startDate', DateType::class, ['widget' => 'choice'])
            ->add('endDate', DateType::class, ['widget' => 'choice'])
            ->add('building', EntityType::class, [
                'class' => Building::class,
            ])
            ->add('room')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BatchTutoringSessionCreationModel::class,
            'csrf_protection' => false,
        ]);
    }
}

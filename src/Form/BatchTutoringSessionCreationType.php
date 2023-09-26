<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Tutoring;
use App\Model\BatchTutoringSessionCreationModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BatchTutoringSessionCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tutoring', EntityType::class, [
                'class' => Tutoring::class,
            ])
            ->add('weekDays', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('startTime', TimeType::class, ['widget' => 'choice'])
            ->add('endTime', TimeType::class, ['widget' => 'choice'])
            ->add('startDate', DateType::class, ['widget' => 'choice'])
            ->add('endDate', DateType::class, ['widget' => 'choice'])
            ->add('building', EntityType::class, [
                'class' => Building::class,
            ])
            ->add('room')
            ->add('saveDefaultValues', CheckboxType::class, [
                'false_values' => ['false'],
            ])
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

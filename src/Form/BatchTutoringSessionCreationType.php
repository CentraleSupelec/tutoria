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
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('tutoring', EntityType::class, [
                'class' => Tutoring::class,
            ])
            ->add('weekDays', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('startTime', TimeType::class)
            ->add('endTime', TimeType::class)
            ->add('startDate', DateType::class)
            ->add('endDate', DateType::class)
            ->add('building', EntityType::class, [
                'class' => Building::class,
            ])
            ->add('room')
            ->add('saveDefaultValues', CheckboxType::class, [
                'false_values' => ['false'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => BatchTutoringSessionCreationModel::class,
            'csrf_protection' => false,
        ]);
    }
}

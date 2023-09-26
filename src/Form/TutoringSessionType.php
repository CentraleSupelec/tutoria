<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Tutoring;
use App\Entity\TutoringSession;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutoringSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tutoring', EntityType::class, [
                'class' => Tutoring::class,
            ])
            ->add('startDateTime', DateTimeType::class, [
                'input' => 'datetime',
                'widget' => 'choice',
            ])
            ->add('endDateTime', DateTimeType::class, [
                'input' => 'datetime',
                'widget' => 'choice',
                'input_format' => 'H:i',
            ])
            ->add('building', EntityType::class, [
                'class' => Building::class,
            ])
            ->add('room')
            ->add('onlineMeetingUri')
            ->add('isRemote', CheckboxType::class, [
                'false_values' => ['false'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TutoringSession::class,
            'csrf_protection' => false,
        ]);
    }
}

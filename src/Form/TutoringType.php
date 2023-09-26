<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Tutoring;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutoringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('defaultWeekDays', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('defaultStartTime', TimeType::class, [
                'input' => 'datetime',
                'widget' => 'choice',
            ])
            ->add('defaultEndTime', TimeType::class, [
                'input' => 'datetime',
                'widget' => 'choice',
            ])
            ->add('defaultBuilding', EntityType::class, [
                'class' => Building::class,
            ])
            ->add('defaultRoom')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tutoring::class,
            'csrf_protection' => false,
        ]);
    }
}

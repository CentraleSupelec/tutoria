<?php

namespace App\Form;

use App\Entity\Tutoring;
use App\Model\TutoringSessionSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutoringSessionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tutorings', EntityType::class, [
                'class' => Tutoring::class,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TutoringSessionSearch::class,
            'csrf_protection' => false,
        ]);
    }
}

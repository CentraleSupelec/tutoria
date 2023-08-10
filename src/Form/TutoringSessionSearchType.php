<?php

namespace App\Form;

use App\Entity\Tutoring;
use App\Model\TutoringSessionSearch;
use App\Repository\TutoringRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutoringSessionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('tutoring', EntityType::class, [
                'class' => Tutoring::class,
                'multiple' => true,
                'label' => false,
                'required' => false,
                'expanded' => false,
                'query_builder' => fn (TutoringRepository $tutoringRepository) => $tutoringRepository->createQueryBuilder('t')
                    ->orderBy('t.name'),
                'choice_label' => fn (Tutoring $tutoring) => $tutoring->getName(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => TutoringSessionSearch::class,
            'tutorings' => [],
        ]);
    }
}

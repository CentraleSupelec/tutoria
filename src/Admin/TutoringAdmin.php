<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\AcademicLevel;
use App\Entity\Building;
use App\Entity\Student;
use App\Repository\StudentRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class TutoringAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('name')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name')
            ->add('academicLevel', EntityType::class, [
                'class' => AcademicLevel::class,
                'required' => true,
                'multiple' => false,
            ])
            ->add('defaultBuilding', EntityType::class, [
                'class' => Building::class,
                'required' => true,
                'multiple' => false,
            ])
            ->add('tutors', EntityType::class, [
                'class' => Student::class,
                'query_builder' => fn (StudentRepository $er) => $er->findStudentByRoleQueryBuilder(Student::ROLE_TUTOR),
                'required' => true,
                'multiple' => true,
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('name')
        ;
    }

    protected function configureFormOptions(array &$formOptions): void
    {
        parent::configureFormOptions($formOptions);

        $formOptions['validation_groups'] = ['AdminTutoringGroup'];
    }
}

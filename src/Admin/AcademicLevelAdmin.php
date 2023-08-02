<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class AcademicLevelAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('nameFr')
            ->add('nameEn')
            ->add('academicYear')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('nameFr')
            ->add('nameEn')
            ->add('academicYear')
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
            ->add('nameFr')
            ->add('nameEn')
            ->add('academicYear')
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('nameFr')
            ->add('nameEn')
            ->add('academicYear')
        ;
    }
}

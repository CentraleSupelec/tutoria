<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Campus;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class BuildingAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name')
            ->add('campus', null, [
                'associated_property' => 'name',
            ])
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
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'required' => true,
                'multiple' => false,
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name')
            ->add('campus', null, [
                'associated_property' => 'name',
            ])
        ;
    }
}

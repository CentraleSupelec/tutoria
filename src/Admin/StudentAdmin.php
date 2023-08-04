<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Student;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class StudentAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('roles')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('roles', null, [
                'template' => 'admin/user_list_roles.html.twig',
                'header_style' => 'width: 10%',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'Prendre la place' => [
                        'template' => 'sonata/CRUD/list__action_student_impersonate.html.twig',
                    ],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => Student::getPossibleRoles(),
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('roles')
        ;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('student_admin_impersonate', $this->getRouterIdParameter().'/impersonate');
    }
}

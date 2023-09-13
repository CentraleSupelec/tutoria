<?php

declare(strict_types=1);

namespace App\Admin\Lti;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class PlatformAdmin extends AbstractAdmin
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
            ->add('name', null, ['label' => 'Nom de la plateforme'])
            ->add('audience', null, ['label' => 'URL de base'])
            ->add('oidcAuthenticationUrl', null, ['label' => "URL d'authentification"])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', null, ['label' => 'Nom de la plateforme'])
            ->add('audience', null, ['label' => 'URL de base'])
            ->add('oidcAuthenticationUrl', null, ['label' => "URL d'authentification"])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name', null, ['label' => 'Nom de la plateforme'])
            ->add('audience', null, ['label' => 'URL de base'])
            ->add('oidcAuthenticationUrl', null, ['label' => "URL d'authentification"])
        ;
    }
}

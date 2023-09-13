<?php

declare(strict_types=1);

namespace App\Admin\Lti;

use App\Entity\Lti\Platform;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class RegistrationAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('clientId')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('clientId', null, ['label' => 'Cient ID'])
            ->add('platform', null, [
                'associated_property' => 'name',
                'label' => 'Plateforme',
            ])
            ->add('deploymentId', null, ['label' => 'ID de déploiement'])
            ->add('platformJwksUrl', null, ['label' => 'Lien de la clé de la plateforme'])
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
            ->add('clientId', null, ['label' => 'Cient ID'])
            ->add('platform', EntityType::class, [
                'label' => 'Plateforme',
                'class' => Platform::class,
                'required' => true,
                'multiple' => false,
            ])
            ->add('deploymentId', null, ['label' => 'ID de déploiement'])
            ->add('platformJwksUrl', null, ['label' => 'Lien de la clé de la plateforme'])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('clientId', null, ['label' => 'Cient ID'])
            ->add('platform', null, [
                'label' => 'Plateforme',
                'associated_property' => 'name',
            ])
            ->add('deploymentId', null, ['label' => 'ID de déploiement'])
            ->add('platformJwksUrl', null, ['label' => 'Lien de la clé de la plateforme'])
        ;
    }
}

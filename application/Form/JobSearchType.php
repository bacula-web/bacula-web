<?php

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of the Bacula-Web project.
 *
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see
 * <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Form;

use App\Entity\Bacula\Client;
use App\Entity\Bacula\JobSearch;
use App\Entity\Bacula\Pool;
use App\Entity\Bacula\Repository\ClientRepository;
use App\Entity\Bacula\Repository\PoolRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class JobSearchType extends AbstractType
{
    private ClientRepository $clientRepository;
    private TranslatorInterface $translator;
    private PoolRepository $poolRepository;

    public function __construct(ClientRepository $clientRepository, TranslatorInterface $translator, PoolRepository $poolRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->translator = $translator;
        $this->poolRepository = $poolRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Any' => null,
                    'Running' => 'Running',
                    'Waiting' => 'Waiting',
                    'Completed' => 'Completed',
                    'Completed with errors' => 'Completed with errors',
                    'Failed' => 'Failed',
                    'Cancelled' => 'Cancelled'
                ],
                'attr' => [
                    'class' => 'form-select-sm'
                ]
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'label' => 'Client',
                'placeholder' => $this->translator->trans('Any'),
                'required' => false,
                'choices' => $this->clientRepository->findBy([], ['name' => 'ASC']),
                'attr' => [
                    'class' => 'form-select-sm'
                ]
            ])
            ->add('level', ChoiceType::class, [
                'choices' => [
                    'Any' => null,
                    'Differential' => 'D',
                    'Incremental' => 'I',
                    'Full' => 'F',
                    'InitCatalog' => 'V',
                    'Catalog' => 'C',
                    'VolumeToCatalog' => 'O',
                    'DiskToCatalog' => 'd',
                    'Data' => 'A'
                ],
                'attr' => [
                    'class' => 'form-select-sm'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Any' => null,
                    'Backup' => 'B',
                    'Migrated' => 'M',
                    'Verify' => 'V',
                    'Restore' => 'R',
                    'Admin' => 'D',
                    'Archive' => 'A',
                    'Copy' => 'C',
                    'Migration' => 'g',
                ],
                'attr' => [
                    'class' => 'form-select-sm'
                ]
            ])
            ->add('pool', EntityType::class, [
                'class' => Pool::class,
                'label' => 'Pool',
                'placeholder' => $this->translator->trans('Any'),
                'required' => false,
                'choices' => $this->poolRepository->findBy([], ['name' => 'ASC']),
                'attr' => [
                    'class' => 'form-select-sm',
                ]
            ])
            ->add('starttime', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('endtime', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('orderby', ChoiceType::class, [
                'choices' => [
                    'Job Scheduled Time' => 'j.scheduledTime',
                    'Job Start Date' => 'j.starttime',
                    'Job End Date' => 'j.endtime',
                    'Job Id' => 'j.id',
                    'Job Name' => 'j.name',
                    'Job Bytes' => 'j.jobbytes',
                    'Job Files' => 'j.jobfiles',
                    'Pool Name' => 'p.name'
                ],
                'label' => 'Order By',
                'attr' => [
                    'class' => 'form-select-sm'
                ]
            ])
            ->add('orderByDirection', ChoiceType::class, [
                'choices' => [
                    'Descending' => 'DESC',
                    'Ascending' => 'ASC'
                ],
                'label' => 'Order By Direction',
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-select-sm'
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobSearch::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}

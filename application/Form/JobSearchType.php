<?php

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of Bacula-Web.
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
use App\Entity\Bacula\Pool;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Bacula\JobSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Running' => 'running',
                    'Waiting' => 'waiting',
                    'Completed' => 'completed',
                    'Completed with errors' => 'completed-with-errors',
                    'Failed' => 'failed',
                    'Cancelled' => 'cancelled'
                ],
                'placeholder' => 'Any',
                'required' => false,
                'attr' => [
                    'class' => 'form-select-sm'
                ],
                'invalid_message' => 'Invalid job status',
            ])
            ->add('level', ChoiceType::class, [
                'choices' => [
                    'Differential' => 'D',
                    'Incremental' => 'I',
                    'Full' => 'F',
                    'InitCatalog' => 'V',
                    'Catalog' => 'C',
                    'VolumeToCatalog' => 'O',
                    'DiskToCatalog' => 'd',
                    'Data' => 'A',
                ],
                'placeholder' => 'Any',
                'required' => false,
                'attr' => [
                    'class' => 'form-select-sm'
                ],
                'invalid_message' => 'Invalid job level',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Backup' => 'B',
                    'Migrated' => 'M',
                    'Verify' => 'V',
                    'Restore' => 'R',
                    'Admin' => 'D',
                    'Archive' => 'A',
                    'Copy' => 'C',
                    'Migration' => 'g',
                ],
                'placeholder' => 'Any',
                'required' => false,
                'attr' => [
                    'class' => 'form-select-sm'
                ],
                'invalid_message' => 'Invalid job type',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'placeholder' => 'Any',
                'required' => false,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select-sm'
                ],
                'invalid_message' => 'Invalid client',
            ])
            ->add('pool', EntityType::class, [
                'class' => Pool::class,
                'choice_label' => 'name',
                'label' => 'Pool',
                'placeholder' => 'Any',
                'required' => false,
                'attr' => [
                    'class' => 'form-select-sm',
                ],
                'invalid_message' => 'Invalid pool',
            ])
            ->add('starttime', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'form-control-sm'
                ],
                'invalid_message' => 'Invalid job start time',
            ])
            ->add('endtime', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'attr' => [
                    'class' => 'form-control-sm'
                ],
                'invalid_message' => 'Invalid job end time',
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
                'data' => 'j.id',
                'label' => 'Order By',
                'attr' => [
                    'class' => 'form-select-sm'
                ],
                'invalid_message' => 'Invalid job order by'
            ])

            ->add('order_direction', CheckboxType::class, [
                'required' => false,
                'label' => 'Ascending',
                'invalid_message' => 'Invalid job order direction',
            ])
            ->add('apply', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary btn-sm'
                ]
            ])
            ->add('reset', ResetType::class, [
                'attr' => [
                    'class' => 'btn btn-sm btn-outline-dark'
                ]
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobSearch::class,
            'method' => 'GET'
        ]);
    }
}

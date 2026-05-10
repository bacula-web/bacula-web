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

namespace App\Form;

use App\Entity\Bacula\Job;
use App\Entity\Bacula\Repository\JobRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackupJobType extends AbstractType
{
    private JobRepository $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('period', ChoiceType::class, [
                'choices' => [
                    'Last 7 days' => 7,
                    'Last 14 days' => 14,
                    'Last 30 days' => 30
                ],
                'invalid_message' => 'Invalid period'
            ])
            ->add('backupjob_name', EntityType::class, [
                'class' => Job::class,
                'label' => 'Job name',
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $entityRepository): QueryBuilder {
                    return $entityRepository
                        ->createQueryBuilder('j')
                        ->groupBy('j.name')
                        ->orderBy('j.name', 'ASC');
                },
                'invalid_message' => 'Invalid job name'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'View report'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

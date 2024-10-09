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

use App\Entity\Bacula\Job;
use App\Entity\Bacula\JobSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderby', ChoiceType::class, [
                'choices' => [
                    'Job Scheduled Time' => 'j.scheduledTime' ,
                    'Job Start Date' => 'j.starttime',
                    'Job End Date' => 'j.endtime',
                    'Job Id' => 'j.id',
                    'Job Name' => 'j.name',
                    'Job Bytes' => 'j.jobbytes',
                    'Job Files' => 'j.jobfiles',
                    'Pool Name' => 'p.name'
                ]
            ]);
/*        $builder
            ->add('name')
            ->add('level')
            ->add('jobbytes')
            ->add('readbytes')
            ->add('jobfiles')
            ->add('type')
            ->add('poolid')
            ->add('starttime')
            ->add('endtime')
            ->add('scheduledTime')
            ->add('clientid')
            ->add('pool')
            ->add('status')
            ->add('client')
        ;*/

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobSearch::class,
            'method' => 'GET',
        ]);
    }
}

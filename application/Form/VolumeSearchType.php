<?php

/**
 * Copyright (C) 2017-present Davide Franco
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

use App\Entity\Bacula\Pool;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VolumeSearch;

class VolumeSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pool', EntityType::class, [
                'attr' => [
                    'class' => 'form-select-sm'
                ],
                'class' => Pool::class,
                'choice_label' => 'name',
                'placeholder' => 'Any',
                'required' => false,
                'invalid_message' => 'Invalid pool'
            ])
            ->add('orderby', ChoiceType::class, [
                'label' => 'Order by',
                'attr' => [
                    'class' => 'form-select-sm'
                ],
                'choices' => [
                    'Name' => 'name',
                    'Id' => 'id',
                    'Bytes' => 'volbytes',
                    'Jobs' => 'voljobs'
                ],
                'data' => 'name',
                'required' => false,
                'invalid_message' => 'Invalid order by'
            ])
            ->add('order_direction', CheckboxType::class, [
                'required' => false,
                'label' => 'Asc',
            ])
            ->add('in_changer', CheckboxType::class, [
                'required' => false,
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
            'data_class' => VolumeSearch::class,
            'method' => 'get'
        ]);
    }
}

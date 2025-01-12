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

use App\Entity\Bacula\Pool;
use App\Entity\Bacula\Repository\PoolRepository;
use App\Entity\Bacula\VolumeSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class VolumeSearchType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var PoolRepository
     */
    private PoolRepository $poolRepository;

    /**
     * @param TranslatorInterface $translator
     * @param PoolRepository $poolRepository
     */
    public function __construct(
        TranslatorInterface $translator,
        PoolRepository $poolRepository
    )
    {

        $this->translator = $translator;
        $this->poolRepository = $poolRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pool', EntityType::class, [
                'class' => Pool::class,
                'label' => 'Pool',
                'placeholder' => $this->translator->trans('Any'),
                'required' => false,
                'mapped' => true,
                'choices' => $this->poolRepository->findBy([], ['name' => 'ASC']),
                'attr' => [
                    'class' => 'form-select-sm'
                ]
            ])
            ->add('order_by', ChoiceType::class, [
                'label' => 'Order by',
                'choices' => [
                    'Name' => 'name',
                    'Id' => 'id',
                    'Bytes' => 'volbytes',
                    'Jobs' => 'voljobs'
                ],
                'required' => false,
                'mapped' => true,
                'data' => 'name',
                'attr' => [
                    'class' => 'form-select-sm'
                ]
            ])
            ->add('order_direction', ChoiceType::class, [
                'label' => 'Order direction',
                'attr' => [
                    'class' => 'form-select-sm'
                ],
                'choices' => [
                    'Descending' => 'DESC',
                    'Ascending' => 'ASC',
                ],
                'expanded' => false,
                'multiple' => false,
                'mapped' => true,
            ])
            ->add('in_changer', CheckboxType::class, [
                'label_attr' => ['class' => 'checkbox-switch'],
                'attr' => [
                    'role' => 'switch',
                ],
                'label' => 'In changer',
                'mapped' => true,
                'required' => false
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VolumeSearch::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }
}

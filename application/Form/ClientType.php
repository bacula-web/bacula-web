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
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClientType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator) {

        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        /**
         * TODO: setup validation for period and client input fields
         */

        $builder
            ->add('period', ChoiceType::class, [
                'placeholder' => $this->translator->trans('Select a period'),
                'required' => true,
                'choices' => [
                    'Last week' => 7,
                    'Last 2 weeks' => 14,
                    'Last month' => 30
                    ]
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'placeholder' => $this->translator->trans('Select a client'),
                'required' => true,
                'query_builder' => function (EntityRepository $clientRepository) {
                    return $clientRepository
                        ->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC')
                        ->distinct();
                },
                'choice_label' => 'name'
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'method' => 'GET',
            'csrf_protection' => false,
            'help' => 'Choose the client name and the period interval and click on <b>View report</b>',
            'help_html' => true
        ]);
    }
}

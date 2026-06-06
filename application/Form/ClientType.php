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

use App\Entity\Bacula\Client;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', EntityType::class, [
                'required' => true,
                'class' => Client::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    if ($this->parameterBag->get('app.show_inactive_clients'))
                    {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    }
                    return $er->createQueryBuilder('c')
                        ->where('c.fileRetention > 0')
                        ->andWhere('c.jobRetention > 0')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'invalid_message' => 'Invalid client provided',
            ])
            ->add('period', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Last 7 days' => '7',
                    'Last 14 days' => '14',
                    'Last 30 days' => '30'
                ],
                'invalid_message' => 'Invalid period provided',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'View report',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET'
        ]);
    }
}

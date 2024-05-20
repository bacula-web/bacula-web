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

namespace App\Controller;

use App\Entity\Core\Repository\UserRepository;
use App\Entity\Core\User;
use App\Form\CreateUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/settings", name="app_settings")
     *
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @return Response
     */
    public function index(
        Request $request,
        ParameterBagInterface $parameterBag,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $users = $this->userRepository->findAll();
        $configDatetimeFormat = $parameterBag->get('app.datetime_format') ?? 'Y-m-d H:i:s';
        $configDateTimeFormatShort = $parameterBag->get('app.datetime_format_short') ?? 'Y-m-d';
        $configLanguage = $parameterBag->get('app.language') ?? 'en_US';
        $configShowInactiveClients = $parameterBag->get('app.show_inactive_clients') ?? false;
        $configHideEmptyPools = $parameterBag->get('app.hide_empty_pools') ?? false;

        /*
         * TODO: remove $basepath from user config and make sure documentation is updated
           $configBasePath = $this->config->get('basepath', null);
        */

        $user = new User();
        $newUserForm = $this->createForm(CreateUserFormType::class, $user);

        $newUserForm->handleRequest($request);

        if ($newUserForm->isSubmitted() && $newUserForm->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword(
                $user,
                $newUserForm->get('password')->getData()
            ));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'User successfully created');
            return $this->redirectToRoute('app_settings');
        }

        return $this->render('pages/settings.html.twig', [
            'users' => $users,
            'config_datetime_format' => $configDatetimeFormat,
            'config_datetime_format_short' => $configDateTimeFormatShort,
            'config_language' => $configLanguage,
            'config_show_inactive_clients' => $configShowInactiveClients,
            'config_hide_empty_pools' => $configHideEmptyPools,
            'new_user_form' => $newUserForm->createView()
        ]);
    }
}

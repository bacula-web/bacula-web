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

namespace App\Controller;

use App\Entity\User;
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
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(path="/settings", name="app_settings")
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
    ): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $configDatetimeFormat = $parameterBag->get('app_datetime_format') ?? 'Y-m-d H:i:s';

        $configDateTimeFormatShort = $parameterBag->get('app_datetime_format_short') ?? 'Y-m-d';

        /*
        // Check if language is set
        $tplData['config_language'] = $this->config->get('language', 'en_US (default value)');

        if ($this->config->has('show_inactive_clients')) {
            $config_show_inactive_clients = $this->config->get('show_inactive_clients');

            if ($config_show_inactive_clients === true) {
                $tplData['config_show_inactive_clients'] = 'checked';
            }
        }

        if ($this->config->has('hide_empty_pools')) {
            $config_hide_empty_pools = $this->config->get('hide_empty_pools');

            if ($config_hide_empty_pools === true) {
                $tplData['config_hide_empty_pools'] = 'checked';
            }
        } else {
            $tplData['config_hide_empty_pools'] = '';
        }

        // Parameter <enable_users_auth> is enabled by default (in case is not specified in config file)
        $config_enable_users_auth = true;

        if ($this->config->has('enable_users_auth') && is_bool($this->config->get('enable_users_auth'))) {
            $config_enable_users_auth = $this->config->get('enable_users_auth');
        }

        // Parameter <debug> is disabled by default (in case is not specified in config file)
        $config_debug = false;

        if ($this->config->has('debug') && is_bool($this->config->get('debug'))) {
            $config_debug = $this->config->get('debug');
        }

        if ($config_debug === true) {
            $tplData['config_debug'] = 'checked';
        } else {
            $tplData['config_debug'] = '';
        }
        */

        /*
         * TODO: remove $basepath from user config and make sure documentation is updated
        $configBasePath = $this->config->get('basepath', null);

        if ($configBasePath == null) {
            $tplData['config_basepath'] = 'not set';
        } else {
            $tplData['config_basepath'] = $configBasePath;
        }
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
            'new_user_form' => $newUserForm->createView()
        ]);
    }
}

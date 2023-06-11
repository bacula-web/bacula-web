<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2023 Davide Franco
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

namespace App\Controller;

use App\Libs\FileConfig;
use App\Tables\UserTable;
use Core\App\View;
use Core\Exception\AppException;
use Core\Helpers\Sanitizer;
use Core\Exception\ConfigFileException;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Valitron\Validator;

class SettingsController
{
    private View $view;
    private UserTable $userTable;
    private SessionInterface $session;

    /**
     * @param View $view
     * @param UserTable $userTable
     * @param SessionInterface $session
     */
    public function __construct(View $view, UserTable $userTable, SessionInterface $session)
    {
        $this->view = $view;
        $this->userTable = $userTable;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ConfigFileException
     * @throws \SmartyException
     */
    public function index(Request $request, Response $response): Response
    {
        $flash = $this->session->getFlash();

        if($flash->has('info')) {
            $this->view->set('flash', $flash->get('info'));
            $flash->clear();
        }

        // Get parameters set in configuration file
        if (!FileConfig::open(CONFIG_FILE)) {
            throw new ConfigFileException("The configuration file is missing");
        } else {
            // Check if datetime_format is set, otherwise, set default datetime_format
            if (FileConfig::get_Value('datetime_format') != null) {
                $this->view->set('config_datetime_format', FileConfig::get_Value('datetime_format'));
            } else {
                $this->view->set('config_datetime_format', 'Y-m-d H:i:s');
            }

            // Check if language is set
            if (FileConfig::get_Value('language') != null) {
                $this->view->set('config_language', FileConfig::get_Value('language'));
            }

            // Check if show_inactive_clients is set
            if (FileConfig::get_Value('show_inactive_clients') != null) {
                $config_show_inactive_clients = FileConfig::get_Value('show_inactive_clients');

                if ($config_show_inactive_clients == true) {
                    $this->view->set('config_show_inactive_clients', 'checked');
                }
            }

            // Check if hide_empty_pools is set
            if (FileConfig::get_Value('hide_empty_pools') != null) {
                $config_hide_empty_pools = FileConfig::get_Value('hide_empty_pools');

                if ($config_hide_empty_pools == true) {
                    $this->view->set('config_hide_empty_pools', 'checked');
                }
            } else {
                $this->view->set('config_hide_empty_pools', '');
            }

            // Parameter <enable_users_auth> is enabled by default (in case is not specified in config file)
            $config_enable_users_auth = true;

            // If enable_users_auth is defined in config file, take the value
            if (FileConfig::get_Value('enable_users_auth') !== null && is_bool(FileConfig::get_Value('enable_users_auth'))) {
                $config_enable_users_auth = FileConfig::get_Value('enable_users_auth');
            }

            if ($config_enable_users_auth === true) {

                // Get users list
                $this->view->set('users', $this->userTable->getAll());

                $this->view->set('config_enable_users_auth', 'checked');
            } else {
                $this->view->set('config_enable_users_auth', '');
            }

            // Parameter <debug> is disabled by default (in case is not specified in config file)
            $config_debug = false;

            // If debug is defined in config file, take the value
            if (FileConfig::get_Value('debug') !== null && is_bool(FileConfig::get_Value('debug'))) {
                $config_debug = FileConfig::get_Value('debug');
            }

            if ($config_debug === true) {
                $this->view->set('config_debug', 'checked');
            } else {
                $this->view->set('config_debug', '');
            }
        }

        $response->getBody()->write($this->view->render('settings.tpl'));
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws AppException
     * @throws \SmartyException
     */
    public function addUser(Request $request, Response $response): Response
    {
        $postData = $request->getParsedBody();

        $form_data = [
            'username' => Sanitizer::sanitize($postData['username']),
            'password' => $postData['password'],
            'email' => Sanitizer::sanitize($postData['email'])
        ];

        $v = new Validator($form_data);

        $v->rule('required', ['username', 'password', 'email']);
        $v->rule('alphaNum', 'username');
        $v->rule('lengthMin', 'password', 8);
        $v->rule('email', 'email');

        if (!$v->validate()) {
            $flash = $this->session->getFlash();
            $flash->add('info', 'Invalid user data provided');
            $this->session->save();
        }

        $result = $this->userTable->addUser(
            $form_data['username'],
            $form_data['email'],
            $form_data['password']
        );

        if ($result !== false) {
            $flash = $this->session->getFlash();
            $flash->add('info', 'User successfully created');
            $this->session->save();
        }

        return $response
            ->withHeader('Location', '/settings')
            ->withStatus(302);
    }
}

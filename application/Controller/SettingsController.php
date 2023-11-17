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
use Slim\Views\Twig;
use Core\Exception\AppException;
use Core\Helpers\Sanitizer;
use Core\Exception\ConfigFileException;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

class SettingsController
{
    private Twig $view;
    private UserTable $userTable;
    private SessionInterface $session;

    /**
     * @param Twig $view
     * @param UserTable $userTable
     * @param SessionInterface $session
     */
    public function __construct(Twig $view, UserTable $userTable, SessionInterface $session)
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response): Response
    {
        $tplData = [];

        // Get parameters set in configuration file
        if (!FileConfig::open(CONFIG_FILE)) {
            throw new ConfigFileException("The configuration file is missing");
        } else {
            // Check if datetime_format is set, otherwise, set default datetime_format
            if (FileConfig::get_Value('datetime_format') != null) {
                $tplData['config_datetime_format'] = FileConfig::get_Value('datetime_format');
            } else {
                $tplData['config_datetime_format'] = 'Y-m-d H:i:s';
            }

            // datetime_format_short
            if (FileConfig::get_Value('datetime_format_short') != null) {
                $tplData['config_datetime_format_short'] = FileConfig::get_Value('datetime_format_short');
            } else {
                $tplData['config_datetime_format_short'] = explode(' ', FileConfig::get_Value('datetime_format'));
                $tplData['config_datetime_format_short'] = $tplData['config_datetime_format_short'][0];
            }

            // Check if language is set
            if (FileConfig::get_Value('language') != null) {
                $tplData['config_language'] = FileConfig::get_Value('language');
            }

            // Check if show_inactive_clients is set
            if (FileConfig::get_Value('show_inactive_clients') != null) {
                $config_show_inactive_clients = FileConfig::get_Value('show_inactive_clients');

                if ($config_show_inactive_clients == true) {
                    $tplData['config_show_inactive_clients'] = 'checked';
                }
            }

            // Check if hide_empty_pools is set
            if (FileConfig::get_Value('hide_empty_pools') != null) {
                $config_hide_empty_pools = FileConfig::get_Value('hide_empty_pools');

                if ($config_hide_empty_pools == true) {
                    $tplData['config_hide_empty_pools'] = 'checked';
                }
            } else {
                $tplData['config_hide_empty_pools'] = '';
            }

            // Parameter <enable_users_auth> is enabled by default (in case is not specified in config file)
            $config_enable_users_auth = true;

            // If enable_users_auth is defined in config file, take the value
            if (FileConfig::get_Value('enable_users_auth') !== null && is_bool(FileConfig::get_Value('enable_users_auth'))) {
                $config_enable_users_auth = FileConfig::get_Value('enable_users_auth');
            }

            if ($config_enable_users_auth === true) {
                // Get users list
                $tplData['users'] = $this->userTable->getAll();

                $tplData['config_enable_users_auth'] = 'checked';
            } else {
                $tplData['config_enable_users_auth'] = '';
            }

            // Parameter <debug> is disabled by default (in case is not specified in config file)
            $config_debug = false;

            // If debug is defined in config file, take the value
            if (FileConfig::get_Value('debug') !== null && is_bool(FileConfig::get_Value('debug'))) {
                $config_debug = FileConfig::get_Value('debug');
            }

            if ($config_debug === true) {
                $tplData['config_debug'] = 'checked';
            } else {
                $tplData['config_debug'] = '';
            }
        }

        return $this->view->render($response, 'pages/settings.html.twig', $tplData);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws AppException
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
            $this->session->getFlash()->set('error', ['Invalid user data provided']);
            $this->session->save();
        }

        $result = $this->userTable->addUser(
            $form_data['username'],
            $form_data['email'],
            $form_data['password']
        );

        if ($result !== false) {
            $this->session->getFlash()->set('info', ['User successfully created']);
            $this->session->save();
        }

        return $response
            ->withHeader('Location', '/settings')
            ->withStatus(302);
    }
}

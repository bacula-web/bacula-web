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

use App\Libs\Config;
use App\Table\UserTable;
use Slim\Views\Twig;
use Core\Exception\AppException;
use Core\Helpers\Sanitizer;
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
     * @var string|null
     */
    private ?string $basePath;
    private Config $config;

    /**
     * @param Twig $view
     * @param UserTable $userTable
     * @param SessionInterface $session
     * @param Config $config
     */
    public function __construct(Twig $view, UserTable $userTable, SessionInterface $session, Config $config)
    {
        $this->view = $view;
        $this->userTable = $userTable;
        $this->session = $session;
        $this->config = $config;

        $this->basePath = $this->config->get('basepath', null);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response): Response
    {
        $tplData = [];

        $tplData['config_datetime_format'] = $this->config->get('datetime_format', 'Y-m-d H:i:s (default value)');

        if ($this->config->has('datetime_format_short') ) {
            $tplData['config_datetime_format_short'] = $this->config->get('datetime_format_short');
        } else {
            $datetimeFormatShort = explode(' ',
                $this->config->get('datetime_format', 'Y-m-d H:i:s'));
            $tplData['config_datetime_format_short'] = $datetimeFormatShort[0] . ' (default value)';
        }

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

        /**
         * TODO: split users in a different controller/page
         */

        if ($config_enable_users_auth === true) {
            // Get users list
            $tplData['users'] = $this->userTable->getAll();

            $tplData['config_enable_users_auth'] = 'checked';
        } else {
            $tplData['config_enable_users_auth'] = '';
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

        $configBasePath = $this->config->get('basepath', null);

        if ($configBasePath == null) {
            $tplData['config_basepath'] = 'not set';
        } else {
            $tplData['config_basepath'] = $configBasePath;
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
        $result = false;

        $form_data = [
            'username' => Sanitizer::sanitize($postData['username']),
            'password' => $postData['password'],
            'confirmPassword' => $postData['confirmPassword'],
            'email' => Sanitizer::sanitize($postData['email'])
        ];

        $v = new Validator($form_data);

        $v->rule('required', ['username', 'password', 'confirmPassword', 'email']);
        $v->rule('alphaNum', 'username');
        $v->rule('lengthMin', 'password', 8);
        $v->rule('email', 'email');
        $v->rule('equals','password', 'confirmPassword')->message('Both passwords must match');

        if (!$v->validate()) {
            $validationErrors = $v->errors();
            foreach($validationErrors as $error) {
                $this->session->getFlash()->add('error', $error[0]);
            }
        } else {
            $result = $this->userTable->addUser(
                $form_data['username'],
                $form_data['email'],
                $form_data['password']
            );
        }

        if ($result !== false) {
            $this->session->getFlash()->set('info', ['User successfully created']);
        }

        $this->session->save();

        return $response
            ->withHeader('Location', $this->basePath . '/settings')
            ->withStatus(302);
    }
}

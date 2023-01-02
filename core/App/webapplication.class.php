<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2022 Davide Franco
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

namespace Core\App;

use App\Libs\FileConfig;
use App\Views\LoginView;
use Core\Exception\NotAuthorizedException;
use Core\Exception\PageNotFoundException;
use Core\Helpers\Sanitizer;
use Core\i18n\CTranslation;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class WebApplication
{
    protected $name;
    protected $version;
    protected $view;
    protected $defaultView;
    protected $userauth;
    protected $enable_users_auth;
    protected $request;
    private $response;
    private $session;
    public $translate;                    // Translation class instance
    public $catalog_nb;                // Catalog count
    public $catalog_current_id = 0;    // Selected or default catalog id
    public $datetime_format;
    public $datetime_format_short;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->session = new Session();
    }

    /**
     * Return View class name, or default one based on the request
     *
     * @param array $routes
     * @return string
     * @throws PageNotFoundException
     */
    private function getMatch(array $routes): string
    {
        if ($this->request->query->has('page')) {
            $page_name = Sanitizer::sanitize($this->request->query->get('page'));
            if (!isset($routes[$page_name])) {
                throw new PageNotFoundException('Page not found');
            }
            return '\\App\Views\\' . ucfirst($routes[$page_name]) . 'View';
        }
        return $this->defaultView;
    }

    private function setup()
    {
        try {
            FileConfig::open(CONFIG_FILE);
        } catch(Exception $exception) {
            die($exception->getMessage());
        }

        /*
         * Check if <enable_users_auth> parameter is set in config file and type is boolean
         * If not set, set it to true by default
         */

        if ((FileConfig::get_Value('enable_users_auth') !== null) && is_bool(FileConfig::get_Value('enable_users_auth'))) {
            $this->enable_users_auth = (bool)FileConfig::get_Value('enable_users_auth');
        } else {
            $this->enable_users_auth = true;
        }

        if ($this->enable_users_auth) {
            // Prepare users authentication back-end
            $this->userauth = new UserAuth();

            // Check if database exists and is writable
            $this->userauth->check();
            $this->userauth->checkSchema();
        }

        // Check application config file
        $appConfigFile = CONFIG_DIR . 'application.php';

        if (file_exists($appConfigFile) && is_readable($appConfigFile)) {
            $app = include_once($appConfigFile);

            // Set application properties from config file
            $this->name = $app['name'];
            $this->version = $app['version'];
            $this->defaultView = 'App\\Views\\' . $app['defaultview'];
        } else {
            throw new Exception('Application config file not found, please fix it');
        }

        // login or logout only if users authentication is enabled
        if ($this->enable_users_auth) {
            // First thing first, is the user session already authenticated ?
            if ($this->userauth->authenticated()) {
                $view_name = $this->getMatch($app['routes']);
                $this->view = new $view_name($this->request);
                $this->view->assign('user_authenticated', 'yes');
                $this->view->assign('enable_users_auth', 'true');
            } elseif (Sanitizer::sanitize($this->request->request->get('action')) === 'login') {
                $input_username = Sanitizer::sanitize($this->request->request->get('username'));
                $input_password = $this->request->request->get('password');

                $this->session->set(
                    'user_authenticated',
                    $this->userauth->authUser($input_username, $input_password)
                );

                if ($this->userauth->authenticated()) {
                    $username = Sanitizer::sanitize($this->request->request->get('username'));
                    $this->session->set('username', $username);

                    $view_name = $this->getMatch($app['routes']);
                    $this->view = new $view_name($this->request);

                    $this->view->assign('user_authenticated', 'yes');
                    $this->view->assign('username', $this->session->get('username'));
                    $this->view->assign('enable_users_auth', 'true');
                } else {
                    $this->view = new LoginView($this->request);
                    $this->view->setAlert('bad username or password');
                    $this->view->setAlertType('danger');
                }
            } else {
                $this->view = new LoginView($this->request);
            }

            if ($this->userauth->authenticated()) {
                if ($this->request->request->has('action')) {
                    switch (Sanitizer::sanitize($this->request->request->get('action'))) {
                        case 'logout':
                            $this->userauth->destroySession();
                            $this->view = new LoginView($this->request);
                            $this->view->setAlert('Successfully logged out');
                            $this->view->setAlertType('success');
                    }
                }
            }
        }
    }

    private function init()
    {
        // Loading configuration file parameters
        if (!FileConfig::open(CONFIG_FILE)) {
            throw new Exception("The configuration file is missing");
        } else {
            // Count defined Bacula catalogs
            $this->catalog_nb = FileConfig::count_Catalogs();

            // Check if debug is enabled
            if (FileConfig::get_Value('debug') != null && is_bool(FileConfig::get_Value('debug'))) {
                ini_set('error_reporting', 'E_ALL');
                ini_set('display_errors', 'On');
                ini_set('display_startup_errors', 'Off');
            }

            // Check if datetime_format is defined in configuration
            if (FileConfig::get_Value('datetime_format') != null) {
                $this->datetime_format = FileConfig::get_Value('datetime_format');
                $this->session->set('datetime_format', $this->datetime_format);

                // Get first part of datetime_format
                $this->datetime_format_short = explode(' ', $this->datetime_format);
                $this->session->set('datetime_format_short', $this->datetime_format_short[0]);
            } else {
                // Set default time format
                $this->session->set('datetime_format', 'Y-m-d H:i:s');
                $this->session->set('datetime_format_short', 'Y-m-d');
            }
        }

        // Initialize smarty gettext function
        $language = FileConfig::get_Value('language');
        if ($language == null) {
            throw new Exception('<b>Config error:</b> $config[\'language\'] not set correctly, please check configuration file');
        }

        $this->translate = new CTranslation($language);
        $this->translate->setLanguage();

        // Get catalog_id from http $_GET request
        if ($this->request->query->has('catalog_id')) {
            if (FileConfig::catalogExist($this->request->request->getInt('catalog_id'))) {
                $this->catalog_current_id = $this->request->query->getInt('catalog_id');
                $this->session->set('catalog_id', $this->catalog_current_id);
            } else {
                $this->session->set('catalog_id', 0);
                $this->catalog_current_id = 0;
                // TODO: It should redirect to home with catalog_id = 0 and display a flash message to the user
                throw new Exception('The catalog_id value provided does not correspond to a valid catalog, please verify the config.php file');
            }
        } elseif ($this->session->has('catalog_id')) {
            // Stick with previously selected catalog_id
            $this->catalog_current_id = $this->session->get('catalog_id');
        } else {
            $this->session->set('catalog_id', $this->catalog_current_id);
        }

        // Define catalog id and catalog label
        $this->view->assign('catalog_current_id', $this->catalog_current_id);
        $this->view->assign('catalog_label', FileConfig::get_Value('label', $this->catalog_current_id));


        // Get Bacula catalog list
        $this->view->assign('catalogs', FileConfig::get_Catalogs());
        // Get catalogs count
        $this->view->assign('catalog_nb', $this->catalog_nb);

        // Set app name and version in view
        $this->view->assign('app_name', $this->name);
        $this->view->assign('app_version', $this->version);

        // Set language
        $this->view->assign('language', $language);

        if ($this->request->query->has('page')) {
            $this->view->assign('page', $this->request->query->getAlpha('page'));
        }
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        try {

            $view = new LoginView($this->request);
            $view->prepare($this->request);
            $response = new Response();
            $response->setContent($view->render('login.tpl'));
            $response->send();
            die();
            $this->setup();
            $this->init();

            $this->view->prepare($this->request);

            $this->response = new Response();
            $this->response->setStatusCode(200);
            $this->response->setContent($this->view->render('login.tpl'));
        } catch (PageNotFoundException $exception) {
            $this->response = new Response(CErrorHandler::displayError($exception), 404);
        } catch (NotAuthorizedException $exception) {
            $this->response = new Response(CErrorHandler::displayError($exception), 403);
        } catch (\PDOException $exception) {
            $this->response = new Response(CErrorHandler::displayError($exception), 500);
        } catch (Exception $exception) {
            $this->response = new Response(CErrorHandler::displayError($exception), 500);
        } finally {
            $this->response->send();
        }
    }
}

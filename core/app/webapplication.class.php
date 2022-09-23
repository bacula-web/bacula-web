<?php

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

use Symfony\Component\HttpFoundation\Request;

class WebApplication
{
    protected $name;
    protected $version;
    protected $view;
    protected $defaultView;
    protected $userauth;
    protected $enable_users_auth;
    protected static $request;

    /**
     * @return Request
     */
    public static function getRequest(): Request
    {
        return self::$request;
    }

    public function __construct()
    {
        self::$request = Request::createFromGlobals();
    }

    protected function setup()
    {
        // Start user session
        session_start();

        // Check if <enable_users_auth> parameter is set in config file, enabled by default
        FileConfig::open(CONFIG_FILE) ;

        if ((FileConfig::get_Value('enable_users_auth') !== null) && is_bool(FileConfig::get_Value('enable_users_auth'))) {
            $this->enable_users_auth = FileConfig::get_Value('enable_users_auth');
        } else {
            $this->enable_users_auth = true;
        }

        if ($this->enable_users_auth  === true) {

            // Prepare users authentication back-end
            $appDbBackend = BW_ROOT . '/application/assets/protected/application.db';
            $this->userauth = new UserAuth( DatabaseFactory::getDatabase('sqlite:' . $appDbBackend));
            $this->userauth->check();
            
            // Check if database exists and is writable
            $this->userauth->checkSchema();
        }

        // Check application config file
        $appConfigFile = CONFIG_DIR . 'application.php';

        if (file_exists($appConfigFile) && is_readable($appConfigFile)) {
            $app = include($appConfigFile);

            // Set application properties from config file
            $this->name = $app['name'];
            $this->version = $app['version'];
            $this->defaultView = $app['defaultview'];
        } else {
            throw new Exception('Application config file not found, please fix it');
        }

        // login or logout only if users authentication is enabled
        if ($this->enable_users_auth  === true) {
            if(WebApplication::getRequest()->request->has('action')) {
                switch (WebApplication::getRequest()->request->get('action')) {
                case 'login':
                    $_SESSION['user_authenticated'] = $this->userauth->authUser(
                        WebApplication::getRequest()->request->get('username'),
                        WebApplication::getRequest()->request->get('password')
                    );

                    if ($_SESSION['user_authenticated'] == 'yes') {
                        $user = $this->userauth->getData(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];

                        $this->view = new $this->defaultView();
                        $this->view->assign('user_authenticated', 'yes');
                    }
                    break;
                case 'logout':
                    $_SESSION['user_authenticated'] = 'no';
                   $this->userauth->destroySession();
                }
            }
        } else {
            $this->view = new $this->defaultView();
        }

        // Check if user is already authenticated or <enable_users_auth> is disabled
        if ((isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] == 'yes') || $this->enable_users_auth == false) {

                // Get requested page or set default one
            if (WebApplication::getRequest()->query->has('page')) {
                $pageName = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
                    
                // Check if requested page is a known route
                if (array_key_exists($pageName, $app['routes'])) {
                    $viewName = ucfirst($app['routes'][$pageName]) . 'View';
                        
                    if (class_exists($viewName)) {
                        $this->view = new $viewName;
                    } else {
                        throw new Exception("PHP class $viewName not found");
                    }
                } else {
                    throw new Exception('Requested page does not exist');
                }
            } else {
                $this->view = new $this->defaultView();
            }
        } else {
            // If user is not authenticated, redirect to Login page
            $this->view = new LoginView();
        }

        // Assign enable_users_auth variable to template
        if ($this->enable_users_auth === true) {
            $this->view->assign('enable_users_auth', 'true');
        } else {
            $_SESSION['user_authenticated'] = 'no';
            $this->view->assign('enable_users_auth', 'false');
        }
        
        if (isset($_SESSION['user_authenticated'])) {
            $this->view->assign('user_authenticated', $_SESSION['user_authenticated']);
        }
    } // end function setup()

    public function run()
    {
        try {
            $this->setup();
            $this->init();
            $this->view->prepare();
            $this->view->render();
        } catch (Exception $e) {
            // Display application error here
            CErrorHandler::displayError($e);
            // Render the view
        }
    }
}

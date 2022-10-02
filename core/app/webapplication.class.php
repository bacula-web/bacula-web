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

namespace Core\App;

use App\Libs\FileConfig;
use App\Views\LoginView;
use Core\Db\DatabaseFactory;
use Core\Helpers\Sanitizer;
use Core\i18n\CTranslation;
use Symfony\Component\HttpFoundation\Request;
use Exception;


class WebApplication
{
    protected $name;
    protected $version;
    protected $view;
    protected $defaultView;
    protected $userauth;
    protected $enable_users_auth;
    protected static $request;
    public $translate;                    // Translation class instance
    public $catalog_nb;                // Catalog count
    public $catalog_current_id = 0;    // Selected or default catalog id
    public $datetime_format;
    public $datetime_format_short;

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

    private function setup()
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
            $app = include_once($appConfigFile);

            // Set application properties from config file
            $this->name = $app['name'];
            $this->version = $app['version'];
            $this->defaultView = 'App\\Views\\' . $app['defaultview'];
        } else {
            throw new Exception('Application config file not found, please fix it');
        }

        // login or logout only if users authentication is enabled
        if ($this->enable_users_auth  === true) {
            if (WebApplication::getRequest()->query->has('action')) {
                if (WebApplication::getRequest()->query->get('action') === 'logout') {
                    $this->userauth->destroySession();
                }
            }

            if (WebApplication::getRequest()->request->has('action')) {
                switch (Sanitizer::sanitize(WebApplication::getRequest()->request->get('action'))) {
                    case 'login':
                        $_SESSION['user_authenticated'] = $this->userauth->authUser(
                            Sanitizer::sanitize(WebApplication::getRequest()->request->get('username')),
                            WebApplication::getRequest()->request->get('password')
                        );

                        if ($_SESSION['user_authenticated'] == 'yes') {
                            $username = Sanitizer::sanitize(WebApplication::getRequest()->request->get('username'));

                            $_SESSION['username'] = $username;

                            $this->view = new $this->defaultView();
                            $this->view->assign('user_authenticated', 'yes');
                        }
                        break;

                    case 'logout':
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
                $pageName = Sanitizer::sanitize(WebApplication::getRequest()->query->get('page'));
                    
                // Check if requested page is a known route
                if (array_key_exists($pageName, $app['routes'])) {
                    $viewName = '\\App\Views\\'. ucfirst($app['routes'][$pageName]) . 'View';

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
                ini_set('error_reporting', E_ALL);
                ini_set('display_errors', 'On');
                ini_set('display_startup_errors', 'Off');
            }


            // Check if datetime_format is defined in configuration
            if (FileConfig::get_Value('datetime_format') != null) {
                $this->datetime_format = FileConfig::get_Value('datetime_format');
                $_SESSION['datetime_format'] = $this->datetime_format;

                // Get first part of datetime_format
                $this->datetime_format_short = explode(' ', $this->datetime_format);
                $_SESSION['datetime_format_short'] = $this->datetime_format_short[0];
            } else {
                // Set default time format
                $_SESSION['datetime_format'] = 'Y-m-d H:i:s';
                $_SESSION['datetime_format_short'] = 'Y-m-d';
            }
        }

        // Checking template cache permissions
        if (!is_writable(VIEW_CACHE_DIR)) {
            throw new Exception("The template cache folder <b>" . VIEW_CACHE_DIR . "</b> must be writable by Apache user");
        }

        // Initialize smarty gettext function
        $language = FileConfig::get_Value('language');
        if ($language == null) {
            throw new Exception('<b>Config error:</b> $config[\'language\'] not set correctly, please check configuration file');
        }

        $this->translate = new CTranslation($language);
        $this->translate->set_Language($this->view);

        // Get catalog_id from http $_GET request
        $this->catalog_current_id = WebApplication::getRequest()->request->getInt('catalog_id', 0);
        $_SESSION['catalog_id'] = $this->catalog_current_id;

        if(WebApplication::getRequest()->query->has('catalog_id')) {
            if (FileConfig::catalogExist(WebApplication::getRequest()->request->getInt('catalog_id'))) {
                $this->catalog_current_id = WebApplication::getRequest()->query->getInt('catalog_id');
                $_SESSION['catalog_id'] = $this->catalog_current_id;
            }else {
                $_SESSION['catalog_id'] = 0;
                $this->catalog_current_id = 0;
                // It should redirect to home with catalog_id = 0 and display a flash message to the user
                throw new Exception('The catalog_id value provided does not correspond to a valid catalog, please verify the config.php file');
            }
        }else {
            if (isset($_SESSION['catalog_id'])) {
                // Stick with previously selected catalog_id
                $this->catalog_current_id = $_SESSION['catalog_id'];
            } else {
                $_SESSION['catalog_id'] = $this->catalog_current_id;
            }
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
    }

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

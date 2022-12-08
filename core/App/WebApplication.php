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

namespace Core\App;

use App\Libs\FileConfig;
use App\Views\LoginView;
use Core\Exception\PageNotFoundException;
use Core\Helpers\Sanitizer;
use Core\i18n\CTranslation;
use Core\Utils\ConfigFileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RequestContext;
use Exception;
use Error;

class WebApplication
{
    /**
     * @var WebApplication
     */
    protected static WebApplication $appInstance;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $version;

    /**
     * @var View
     */
    protected View $view;

    protected $defaultView;
    protected $userauth;
    protected $enable_users_auth;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var Session
     */
    private Session $session;

    public $translate;                    // Translation class instance
    public $catalog_nb;                // Catalog count
    public $catalog_current_id = 0;    // Selected or default catalog id
    public $datetime_format;
    public $datetime_format_short;

    /**
     * @var array
     */
    private static array $config;

    /**
     * @var RequestContext
     */
    protected RequestContext $context;

    /**
     * @var string[];
     */
    private array $routes = [];

    /**
     * @param array $config Application config
     */
    public function __construct(array $config)
    {
        // Create session
        $this->session = new Session();

        // Load application config
        self::$config = $config;

        /**
         * Set exception & error handlers
         */
        set_exception_handler([\Core\App\ErrorController::class, 'handle']);

        // Save routes list
        $this->routes = self::$config['routes'];
    }

    /**
     * Return the instance object of type WebApplication
     * The purpose is to use getContext() and getRoutes() method from a different context
     *
     * @return WebApplication
     */
    public static function getApp(): WebApplication
    {
        if (!isset(self::$appInstance))
        {
            self::$appInstance = new WebApplication(require CONFIG_DIR . '/application.php');
        }
        return self::$appInstance;
    }

    /**
     * Return View class name, or default one based on the request
     *
     * @return Response Controller method related to the request
     * @throws PageNotFoundException
     * @throws Exception
     */
    private function invokeController(): Response
    {
        // Inject request params into Request object instance
        $params = $this->request->query->all();
        $params = array_merge($this->request->request->all(), $params);
        $this->request->attributes->add($params);
        $page = $this->request->attributes->get('page');

        /**
         * if page request param is not provided, return fallback controller return response
         */
        if( $page === null) {
            $callback = self::$config['fallback_controller']['callback'];
            return call_user_func([(new $callback($this->request,(new View()))), 'prepare']);
        }

        /**
         * IF the page does exist, return route callback method response
         * Otherwise, if the page does not exist, then return ErrorController::handle() return value (Response) with a 404 status code
         */
        if (array_key_exists($page, self::getRoutes())) {
            $callback = self::$config['routes'][$page]['callback'];
            return call_user_func([(new $callback($this->request, (new View()))), 'prepare']);
        } else {
            return ErrorController::handle((new PageNotFoundException()));
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function setup(): void
    {
        FileConfig::open(CONFIG_FILE);

        /**
         * Check if <enable_users_auth> parameter is set in config file and type is boolean
         * If not set, set it to true by default
         */

        /*
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

        // login or logout only if users authentication is enabled
        if ($this->enable_users_auth) {
            // First thing first, is the user session already authenticated ?
            if ($this->userauth->authenticated()) {
                $view_name = $this->getMatch(self::$config['routes']);
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

                    $view_name = $this->getMatch(self::$config['routes']);
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
        */
    }

    /**
     * This method run some "pre-flight" checks before launch
     *
     * @return void
     * @throws ConfigFileException
     */
    private function bootstrap()
    {
        // Loading configuration file parameters
        if (!FileConfig::open(CONFIG_FILE)) {
            throw new ConfigFileException('The configuration file is missing');
        } else {
            // Count defined Bacula catalogs
            $this->catalog_nb = FileConfig::count_Catalogs();

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
            throw new ConfigFileException('<b>Config error:</b> $config[\'language\'] not set correctly, please check configuration file');
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
                throw new ConfigFileException('The catalog_id value provided does not correspond to a valid catalog, please verify the config.php file');
            }
        } elseif ($this->session->has('catalog_id')) {
            // Stick with previously selected catalog_id
            $this->catalog_current_id = $this->session->get('catalog_id');
        } else {
            $this->session->set('catalog_id', $this->catalog_current_id);
        }

        /*
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
        */
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ConfigFileException
     */
    public function run(Request $request): Response
    {
        $response = null;
        $this->request = $request;

        try {
            /**
             * Initialize smarty gettext function
             */
            FileConfig::open(CONFIG_FILE);

            $language = FileConfig::get_Value('language');
            if ($language == null) {
                throw new ConfigFileException('<b>Config error:</b> $config[\'language\'] not set correctly, please check configuration file');
            }

            $this->translate = new CTranslation($language);
            $this->translate->setLanguage();

            $this->bootstrap();

            $response = $this->invokeController();
        } catch(PageNotFoundException|Exception $exception) {
            return ErrorController::handle($exception);
        } catch(Exception $exception) {
            return ErrorController::handle($exception);
        } catch(Error $error) {
            return ErrorController::handle($error);
        }

        return $response;

        /*
        try {

            $this->init();
        */
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return self::$config['name'];
    }

    /**
     * @return string
     */
    public static function getVersion(): string
    {
        return self::$config['version'];
    }

    /**
     * @return bool|null
     * @throws ConfigFileException
     */
    public function isDebug(): ?bool
    {
        FileConfig::open(CONFIG_DIR . '/config.php');
        try {
            return FileConfig::get_Value('debug') ?? false;
        } catch(ConfigFileException $exception) {
            exit();
        }
    }

    /**
     * @return RequestContext
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * @return string[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}

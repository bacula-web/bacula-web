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
use App\Middleware\ExceptionMiddleware;
use App\Middleware\RouterMiddleware;
use App\Middleware\DbAuthMiddleware;
use Core\Middleware\MiddlewareInterface;
use Core\Exception\ConfigFileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Exception;

/**
 * WebApplication class is the main http request handler
 */
class WebApplication
{
    /**
     * @var WebApplication
     */
    protected static WebApplication $appInstance;

    /**
     * @var View
     */
    protected View $view;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var int
     */
    public int $catalog_nb;

    /**
     * @var int
     */
    public int $catalog_current_id = 0;    // Selected or default catalog id

    /**
     * @var string
     */
    public string $datetime_format;

    /**
     * @var array
     */
    public array $datetime_format_short;

    /**
     * @var array
     */
    private static array $config;

    /**
     * @var string[];
     */
    private static array $routes = [];

    /**
     * @param array $config Application config
     */
    public function __construct(array $config)
    {
        // Create session
        $this->session = new Session();

        // Load application config
        self::$config = $config;

        // Save routes list
        self::$routes = self::$config['routes'];

        /**
         * Load app name and version from application/config/app using phpdotenv
         */
        $dotenv = \Dotenv\Dotenv::createImmutable(CONFIG_DIR, 'app');
        $dotenv->load();
    }

    /**
     * Return the instance object of type WebApplication
     * The purpose is to use getContext() and getRoutes() method from a different context
     *
     * @return WebApplication
     */
    public static function getApp(): WebApplication
    {
        if (!isset(self::$appInstance)) {
            self::$appInstance = new WebApplication(require_once CONFIG_DIR . '/application.php');
        }
        return self::$appInstance;
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
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $this->request = $request;
        $response = new Response();

        try {
            /**
             * Add Database user authentication middleware is enable only if it's enabled
             * in the configuration
             *
             */
            $session = new Session();

            FileConfig::open(CONFIG_FILE);

            $enable_users_auth = ((FileConfig::get_Value('enable_users_auth') !== null) && is_bool(FileConfig::get_Value('enable_users_auth'))) ? (bool)FileConfig::get_Value('enable_users_auth') : true;

            $session->set('enable_users_auth', $enable_users_auth);

            if ($enable_users_auth) {
                $response = $this->pipeMiddleware(new DbAuthMiddleware(), $response);
            }

            /**
             * Add router middleware
             */
            $response = $this->pipeMiddleware(new RouterMiddleware(), $response);

            $this->bootstrap();
        } catch (Exception $exception) {
            /**
             * Exception handler should be the last middleware in the queue
             */
            return $this->pipeMiddleware(new ExceptionMiddleware($exception), $response);
        }

        return $response;
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
        } catch (ConfigFileException $exception) {
            exit();
        }
    }

    /**
     * @return string[]
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * @param MiddlewareInterface $middleware
     * @param Response $response
     * @return Response
     */
    private function pipeMiddleware(MiddlewareInterface $middleware, Response $response): Response
    {
        return $middleware->process($this->request, $response);
    }
}

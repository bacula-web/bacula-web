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

namespace App\Middleware;

use Core\App\UserAuth;
use Core\Exception\NotAuthenticatedException;
use Core\Exception\NotAuthorizedException;
use Core\Middleware\MiddlewareInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class DbAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var UserAuth
     */
    private UserAuth $dbAuth;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->dbAuth = new UserAuth();

        // Check if database exists and is writable
        $this->dbAuth->check();
        $this->dbAuth->checkSchema();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotAuthenticatedException
     */
    public function process(Request $request, Response $response): Response
    {
        $resultesponse = new Response();

        $appConfigFile = CONFIG_DIR . 'application.php';

        /**
         * throw NotAuthenticatedException for all pages except login
         * This avoid an infinite redirect loop to login page
         */
        if (!$this->dbAuth->authenticated()) {
            if ($request->get('page') !== 'login') {
                throw new NotAuthenticatedException();
            }
        }

        $resultesponse->setStatusCode(200);
        $resultesponse->setContent($response->getContent() . '<pre> you are authentified</pre>');
        return $resultesponse;
    }
}

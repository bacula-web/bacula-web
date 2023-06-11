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

use Core\App\View;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class FlashMiddleware
{
    private SessionInterface $session;
    private View $view;

    public function __construct(SessionInterface $session, View $view)
    {
        $this->session = $session;
        $this->view = $view;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        echo '<pre>flash</pre>';

        var_dump($this->session->getFlash()->all());

        die(var_dump($this->session->getFlash()));

        if ($this->session->getFlash()->has('info')) {
            $this->view->set('flash', $this->session->getFlash()->get('info'));

            $this->session->getFlash()->clear();
        }

        return $response;
    }
}
<?php

declare(strict_types=1);

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

namespace App\Middleware;

use App\Libs\Config;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Views\Twig;

class CatalogSelectorMiddleware implements MiddlewareInterface
{
    private SessionInterface $session;
    private Twig $twig;
    private Config $config;

    /**
     * @param SessionInterface $session
     * @param Twig $twig
     * @param Config $config
     */
    public function __construct(SessionInterface $session, Twig $twig, Config $config)
    {
        $this->session = $session;
        $this->twig = $twig;
        $this->config = $config;
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return ResponseInterface
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        /**
         * Make sure the session has catalog_id set
         */
        if (! $this->session->has('catalog_id')) {
            $this->session->set('catalog_id', 0);
        }

        $params = $request->getQueryParams();
        $catalogId = $this->session->get('catalog_id');

        if (isset($params['catalog_id'])) {
            if (array_key_exists($params['catalog_id'], $this->config->getArrays())) {
                $catalogId = (int) $params['catalog_id'];
                $this->session->set('catalog_id', $catalogId);
            } else {
                $this->session->getFlash()->set('error', ['This catalog id does not exists']);
                $catalogId = 0;
            }
        }

        $this->twig->getEnvironment()->addGlobal('catalog_current_id', $catalogId);
        $this->twig->getEnvironment()->addGlobal('catalog_label', $this->config->getArrays()[$catalogId]['label']);

        return $handler->handle($request);
    }
}
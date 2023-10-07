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

use App\Libs\FileConfig;
use Core\Exception\ConfigFileException;
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

    /**
     * @param SessionInterface $session
     * @param Twig $twig
     */
    public function __construct(SessionInterface $session, Twig $twig)
    {
        $this->session = $session;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return ResponseInterface
     * @throws ConfigFileException
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        FileConfig::open(CONFIG_FILE);

        $params = $request->getQueryParams();
        $catalogId = $this->session->get('catalog_id', 0);

        if (isset($params['catalog_id'])) {
            if (FileConfig::catalogExist($catalogId)) {
                $catalogId = (int) $params['catalog_id'];
                $this->session->set('catalog_id', $catalogId);
            } else {
                $this->session->getFlash()->set('error', ['This catalog id does not exists']);
                $catalogId = 0;
            }
        }

        $this->twig->getEnvironment()->addGlobal('catalog_current_id', $catalogId);
        $this->twig->getEnvironment()->addGlobal('catalog_label', FileConfig::get_Value('label', $catalogId));

        return $handler->handle($request);
    }
}
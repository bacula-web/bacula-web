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

namespace Core\Controller;

use App\Libs\Config;
use Core\Db\ManagerRegistry;
use Odan\Session\SessionInterface;
use Slim\Views\Twig;

abstract class AbstractController
{
    /**
     * @var Twig
     */
    protected Twig $view;

    /**
     * @var string|null
     */
    protected ?string $basePath;

    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var ManagerRegistry
     */
    protected ManagerRegistry $managerRegistry;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param Twig $view
     * @param Config $config
     * @param SessionInterface $session
     */
    public function __construct(ManagerRegistry $managerRegistry, Twig $view, Config $config, SessionInterface $session)
    {
        $this->managerRegistry = $managerRegistry;
        $this->view = $view;
        $this->config = $config;
        $this->basePath = $this->config->get('basepath', null);
        $this->session = $session;
    }
}

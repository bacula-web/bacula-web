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

use Core\Helpers\Sanitizer;
use SmartyException;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var View
     */
    protected View $view;

    /**
     * @var string
     */
    protected string $userAlert = '';
    /**
     * @var string
     */
    protected string $userAlertType = '';

    public function __construct(Request $request, View $view)
    {
        $this->request = $request;
        $this->view = $view;
    }

    /**
     * @param string $alert
     * @return void
     */
    public function setAlert(string $alert)
    {
        $this->userAlert = $alert;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setAlertType(string $type)
    {
        $this->userAlertType = $type;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setVar(string $key, $value)
    {
        $this->view->getRenderer()->assign($key, $value);
    }

    /**
     * @param string $templateName
     * @return string
     * @throws SmartyException
     */
    public function render(string $templateName): string
    {
        // Build breadcrumb
        if ($this->request->query->has('page')) {
            $breadcrumb = '<li> <a href="index.php" title="' . _("Back to Dashboard") . '"><i class="fa fa-home fa-fw"></i> Dashboard</a> </li>';
            $breadcrumb .= '<li class="active">' . 'FIX THIS PLEASE' . '</li>';
        } else {
            $breadcrumb = '<li class="active"> <i class="fa-light fa-home fa-fw"></i> ' . 'TO BE FIIIIIXED PLEASE' . '</li>';
        }
        $this->setVar('breadcrumb', $breadcrumb);

        /**
         * Show flash message to user
         * TODO: This needs to be moved somewhere, for separation of concern sake
         */
        $this->setVar('userAlert', $this->userAlert);
        $this->setVar('userAlertType', $this->userAlertType);

        return $this->view->getRenderer()->fetch($templateName);
    }

    /**
     * @param string $parameter
     * @param mixed $default
     * @return mixed|null
     */
    protected function getParameter(string $parameter, $default)
    {
        if ($this->request->getMethod() === 'GET') {
            if ($this->request->query->has($parameter)) {
                return Sanitizer::sanitize($this->request->query->get($parameter));
            }
        } elseif ($this->request->getMethod() === 'POST') {
            if ($this->request->request->has($parameter)) {
                return Sanitizer::sanitize($this->request->request->get($parameter));
            }
        }

        return $default;
    }
}

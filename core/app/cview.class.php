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

use Core\Helpers\Sanitizer;
use Smarty;
use SmartyBC;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class CView extends Smarty
{
    protected $templateName;
    protected $name;
    protected $title;

    protected $userAlert;
    protected $userAlertType;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        parent::__construct();

        $this->request = $request;

        $this->setTemplateDir(VIEW_DIR);
        $this->setCompileDir(VIEW_CACHE_DIR);
        $this->setCacheDir(VIEW_CACHE_DIR);

        $this->force_compile = true;
        $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
    }

    /**
     * @param $alert
     * @return void
     */
    public function setAlert($alert)
    {
        $this->userAlert = $alert;
    }

    public function setAlertType($type)
    {
        $this->userAlertType = $type;
    }

    public function render(Request $request)
    {
        $this->assign('page_name', $this->name);
        $this->assign('page_title', $this->title);
        $this->assign('templateName', $this->templateName);

        // TODO: to move somewhere else, but not keep in the view for sure
        // Set username, if user is connected

        $session = new Session();
        if ($session->has('user_authenticated') && $session->get('user_authenticated') === 'yes') {
            $this->assign('username', $session->get('username'));
        }

        // Give user some feedback
        $this->assign('userAlert', $this->userAlert);
        $this->assign('userAlertType', $this->userAlertType);

        // Build breadcrumb
        if ($request->query->has('page')) {
            $breadcrumb = '<li> <a href="index.php" title="' . _("Back to Dashboard") . '"><i class="fa fa-home fa-fw"></i> Dashboard</a> </li>';
            $breadcrumb .= '<li class="active">' . $this->name . '</li>';
        } else {
            $breadcrumb = '<li class="active"> <i class="fa-light fa-home fa-fw"></i> ' . $this->name . '</li>';
        }
        $this->assign('breadcrumb', $breadcrumb);

        // Render using the default layout
        return $this->fetch('layouts/default.tpl');
    }

    /**
     * @param string $parameter
     * @param mixed $default
     * @return string|null
     */

    protected function getParameter(string $parameter, $default): ?string
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

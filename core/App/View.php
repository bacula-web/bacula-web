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
use App\Libs\FileConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class View
{
    /**
     * @var Smarty
     */
    protected Smarty $renderer;

    /**
     * @var string
     */
    protected string $templateName;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var string
     */
    protected string $title;

    /**
     * @var string
     */
    protected string $userAlert = '';
    /**
     * @var string
     */
    protected string $userAlertType = '';

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var string
     */
    protected string $templatesRoot = BW_ROOT . '/application/views/templates';

    /**
     * @var string
     */
    protected string $cacheDir = BW_ROOT . '/application/views/cache';

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function __construct(Request $request)
    {
        $this->renderer = new Smarty();

        $this->request = $request;

        $this->renderer->setTemplateDir([
            $this->templatesRoot . '/layouts',
            $this->templatesRoot . '/partials',
            $this->templatesRoot . '/pages'
            ]);

        $this->renderer->setCompileDir($this->cacheDir);
        $this->renderer->setCacheDir($this->cacheDir);

        /**
         * Throw an exception if cache directory is not writable by the web server
         */
        if (!is_writable($this->cacheDir)) {
            throw new \Exception("The template cache folder <b>" . $this->cacheDir . "</b> must be writable by Apache user");
        }

        $this->renderer->caching = Smarty::CACHING_LIFETIME_CURRENT;

        FileConfig::open(CONFIG_FILE);
        if (FileConfig::get_Value('debug') === true) {
            $this->renderer->debugging = true;
            $this->renderer->force_compile = true;
        }
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
     * @param string $template
     * @return false|string
     * @throws \Exception
     */
    public function render(string $template)
    {
        // TODO: to move somewhere else, but not keep in the view for sure
        // Set username, if user is connected
        $session = new Session();
        if ($session->has('user_authenticated') && $session->get('user_authenticated') === 'yes') {
            $this->renderer->assign('username', $session->get('username'));
        }

        // Give user some feedback
        $this->renderer->assign('userAlert', $this->userAlert);
        $this->renderer->assign('userAlertType', $this->userAlertType);

        // Build breadcrumb
        if ($this->request->query->has('page')) {
            $breadcrumb = '<li> <a href="index.php" title="' . _("Back to Dashboard") . '"><i class="fa fa-home fa-fw"></i> Dashboard</a> </li>';
            $breadcrumb .= '<li class="active">' . $this->name . '</li>';
        } else {
            $breadcrumb = '<li class="active"> <i class="fa-light fa-home fa-fw"></i> ' . $this->name . '</li>';
        }
        $this->renderer->assign('breadcrumb', $breadcrumb);

        // Render using the default layout
        try {
            return $this->renderer->fetch($template);
        } catch (\Exception $e) {
            throw new \Exception();
        }
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

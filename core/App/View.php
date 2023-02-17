<?php

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

use Core\Exception\AppException;
use Core\Utils\ConfigFileException;
use Smarty;
use App\Libs\FileConfig;
use Symfony\Component\HttpFoundation\Request;

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
     * @throws AppException
     * @throws ConfigFileException
     */
    public function __construct()
    {
        $this->renderer = new Smarty();
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
            throw new AppException("The template cache folder <b>" . $this->cacheDir . "</b> must be writable by Apache user");
        }

        $this->renderer->force_compile = true;
        $this->renderer->caching = Smarty::CACHING_LIFETIME_CURRENT;

        FileConfig::open(CONFIG_FILE);
        if (FileConfig::get_Value('debug') === true) {
            $this->renderer->debugging = true;
        }
    }

    /**
     * @return Smarty
     */
    public function getRenderer(): Smarty
    {
        return $this->renderer;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function set($name, $value): void
    {
        $this->renderer->assign($name, $value);
    }

    /**
     * @param string $template
     * @return string Template content or throw ar either an SmartyException or Exception
     * @throws \Exception
     * @throws \SmartyException
     */
    public function render(string $template): string
    {
        try {
            return $this->renderer->fetch($template);
        } catch (\Exception $e) {
            throw new \SmartyException();
        }
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }
}

<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco			                               |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
 */

class CView extends Smarty
{

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        // Set to true to force template generation if a template has changed
        $this->compile_check = false;

        // Template caching
        $this->cache_dir = VIEW_CACHE_DIR;
        $this->caching = 0;
        $this->cache_lifetime = 60;

        // Set to true for debug or dev purpose only
        $this->debugging = false;
        $this->force_compile = true;

        $this->template_dir = VIEW_DIR;
        $this->compile_dir = VIEW_CACHE_DIR;
    }

    public function render($view = 'index.tpl')
    {
        $this->display($view);
    }
}

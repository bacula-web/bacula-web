<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2020, Davide Franco			                            |
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

class CView extends SmartyBC
{
    protected $templateName;
    protected $name;
    protected $title;

    protected $userAlert;
    protected $userAlertType;

    public function __construct()
    {
        parent::__construct();

        $this->setTemplateDir(VIEW_DIR);
        $this->setCompileDir(VIEW_CACHE_DIR);
        $this->setCacheDir(VIEW_CACHE_DIR);

        $this->force_compile = true;
        $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
    }

    public function render()
    {   
        $this->assign('page_name', $this->name);
        $this->assign('page_title', $this->title);
        $this->assign('templateName', $this->templateName);

        // Set username, if user is connected
        if( isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] == 'yes') {
            $this->assign('username', $_SESSION['username']);
        }

        // Give user some feedback
        $this->assign('userAlert', $this->userAlert);
        $this->assign('userAlertType', $this->userAlertType);

        // Render using the default layout
        $this->display('layouts/default.tpl');
    }
}

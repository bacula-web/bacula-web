<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco			                    		       |
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

 // Class autloader instance creation
 require_once(getcwd() . "/core/app/classautoloader.class.php");
 
 // Add root path and exclusions
 $classloader = new ClassAutoLoader();
 $classloader->add_Path('core');
 $classloader->add_Path('application');
 $classloader->add_Exclusion('core/vendor/smarty-gettext-1.1');
 
 // Get all $_POST and $_GET values
 CHttpRequest::get_Vars();
 
 // Views path
 define('BW_ROOT', getcwd());
 define('VIEW_DIR', BW_ROOT . "/application/view/");
 define('VIEW_CACHE_DIR', BW_ROOT . "/application/view/cache");
 
 // Configuration
 define('CONFIG_DIR', BW_ROOT . "/application/config/");
 define('CONFIG_FILE', CONFIG_DIR . "config.php");
 
 // Locales
 define('LOCALE_DIR', BW_ROOT . '/application/locale');
 
 // Smarty
 require_once(BW_ROOT . '/core/vendor/smarty-gettext-1.1/smarty-gettext.php');
 
 // PHPLot
 require_once(BW_ROOT . '/core/vendor/phplot-6.1.0/phplot.php');

<?php

/**
 * Copyright (C) 2004 Juan Luis Frances Jimenez
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

require_once('core/bootstrap.php');

class Bweb extends WebApplication
{
    public $translate;                    // Translation class instance

    public $catalog_nb;                // Catalog count
    public $catalog_current_id = 0;    // Selected or default catalog id

    public $datetime_format;
    public $datetime_format_short;

    public function init()
    {
        // Loading configuration file parameters
        if (!FileConfig::open(CONFIG_FILE)) {
            throw new Exception("The configuration file is missing");
        } else {
            // Count defined Bacula catalogs
            $this->catalog_nb = FileConfig::count_Catalogs();

            // Check if debug is enabled
            if (FileConfig::get_Value('debug') != null && is_bool(FileConfig::get_Value('debug'))) {
                ini_set('error_reporting', E_ALL);
                ini_set('display_errors', 'On');
                ini_set('display_startup_errors', 'Off');
            }


            // Check if datetime_format is defined in configuration
            if (FileConfig::get_Value('datetime_format') != null) {
                $this->datetime_format = FileConfig::get_Value('datetime_format');
                $_SESSION['datetime_format'] = $this->datetime_format;
                  
                // Get first part of datetime_format
                $this->datetime_format_short = explode(' ', $this->datetime_format);
                $_SESSION['datetime_format_short'] = $this->datetime_format_short[0];
            } else {
                // Set default time format
                $_SESSION['datetime_format'] = 'Y-m-d H:i:s';
                $_SESSION['datetime_format_short'] = 'Y-m-d';
            }
        }
            
        // Checking template cache permissions
        if (!is_writable(VIEW_CACHE_DIR)) {
            throw new Exception("The template cache folder <b>" . VIEW_CACHE_DIR . "</b> must be writable by Apache user");
        }
                
        // Initialize smarty gettext function
        $language = FileConfig::get_Value('language');
        if ($language == null) {
            throw new Exception('<b>Config error:</b> $config[\'language\'] not set correctly, please check configuration file');
        }
                
        $this->translate = new CTranslation($language);
        $this->translate->set_Language($this->view);
            
        // Get catalog_id from http $_GET request
        if (!is_null(CHttpRequest::get_Value('catalog_id'))) {
            if (FileConfig::catalogExist(CHttpRequest::get_Value('catalog_id'))) {
                $this->catalog_current_id = CHttpRequest::get_Value('catalog_id');
                $_SESSION['catalog_id'] = $this->catalog_current_id;
            } else {
                $_SESSION['catalog_id']    = 0;
                $this->catalog_current_id = 0;
                throw new Exception('The catalog_id value provided does not correspond to a valid catalog, please verify the config.php file');
            }
        } else {
            if (isset($_SESSION['catalog_id'])) {
                // Stick with previously selected catalog_id
                $this->catalog_current_id = $_SESSION['catalog_id'];
            } else {
                $_SESSION['catalog_id'] = $this->catalog_current_id;
            }
        }
            
        // Define catalog id and catalog label
        $this->view->assign('catalog_current_id', $this->catalog_current_id);
        $this->view->assign('catalog_label', FileConfig::get_Value('label', $this->catalog_current_id));
            
        
        // Get Bacula catalog list
        $this->view->assign('catalogs', FileConfig::get_Catalogs());
        // Get catalogs count
        $this->view->assign('catalog_nb', $this->catalog_nb);

        // Set app name and version in view
        $this->view->assign('app_name', $this->name);
        $this->view->assign('app_version', $this->version);

        // Set language
        $this->view->assign('language', $language);
    }
} // end class Bweb

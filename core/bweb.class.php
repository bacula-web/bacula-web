<?php
/* 
 +-------------------------------------------------------------------------+
 | Copyright (C) 2004 Juan Luis Francés Jiménez				                  |
 | Copyright 2010-2017, Davide Franco			       		                  |
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

    require_once('core/global.inc.php');

class Bweb
{
    public $translate;                    // Translation class instance
    private $view;                        // Template class

    public $catalog_nb;                // Catalog count
    public $catalog_current_id = 0;    // Selected or default catalog id

    public $datetime_format;
    public $datetime_format_short;

    public function __construct(&$view)
    {
        try {
            // Loading configuration file parameters
            if (!FileConfig::open(CONFIG_FILE)) {
                throw new Exception("The configuration file is missing");
            } else {
               // Count defined Bacula catalogs
               $this->catalog_nb = FileConfig::count_Catalogs();

               // Check if datetime_format is defined in configuration
               if( FileConfig::get_Value('datetime_format') != NULL) {
                  $this->datetime_format = FileConfig::get_Value('datetime_format');
                  
                  // Get first part of datetime_format
                  $this->datetime_format_short = explode( ' ', $this->datetime_format);
                  $this->datetime_format_short = $this->datetime_format_short[0];
               }else {
                  // Set default time format
                  $this->datetime_format = 'Y-m-d H:i:s';
                  $this->datetime_format_short = 'Y-m-d';
               }
            }
        } catch (Exception $e) {
            CErrorHandler::displayError($e);
        }
                
     // Template engine initalization
        $this->view = $view;
            
     // Checking template cache permissions
        if (!is_writable(VIEW_CACHE_DIR)) {
            throw new Exception("The template cache folder <b>" . VIEW_CACHE_DIR . "</b> must be writable by Apache user");
        }
                
     // Initialize smarty gettext function
        $language = FileConfig::get_Value('language');
        if ($language == NULL) {
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
            }else{
               $_SESSION['catalog_id'] = $this->catalog_current_id;
            }
        }
            
            // Define catalog id and catalog label
        $this->view->assign('catalog_current_id', $this->catalog_current_id);
        $this->view->assign('catalog_label', FileConfig::get_Value('label', $this->catalog_current_id));
            
      // Bacula catalog selection
      if ($this->catalog_nb > 1) {
         // Catalogs list
            $this->view->assign('catalogs', FileConfig::get_Catalogs());
         // Catalogs count
            $this->view->assign('catalog_nb', $this->catalog_nb);
        }
    }
} // end class Bweb

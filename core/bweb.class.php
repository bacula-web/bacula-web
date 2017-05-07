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
    private $catalogs = array();        // Catalog array

    private $view;                        // Template class

    public $db_link;                    // Database connection
    private $db_driver;                    // Database connection driver

    public $catalog_nb;                // Catalog count
    public $catalog_current_id = 0;    // Selected or default catalog id

    public $datetime_format;

    public function __construct(&$view)
    {
        // Loading configuration file parameters
        try {
            if (!FileConfig::open(CONFIG_FILE)) {
                throw new Exception("The configuration file is missing");
            } else {
               // Count defined Bacula catalogs
               $this->catalog_nb = FileConfig::count_Catalogs();

               // Check if datetime_format is defined in configuration
               if( FileConfig::get_Value('datetime_format') != false)
                  $this->datetime_format = FileConfig::get_Value('datetime_format');
               else
                  $this->datetime_format = 'Y-m-d H:i:s';
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
        if (!$language) {
            throw new Exception("Language translation problem");
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
            }
        }
            
            // Define catalog id and catalog label
        $this->view->assign('catalog_current_id', $this->catalog_current_id);
        $this->view->assign('catalog_label', FileConfig::get_Value('label', $this->catalog_current_id));
            
        // Getting database connection paremeter from configuration file
        $dsn = FileConfig::get_DataSourceName($this->catalog_current_id);
        $driver = FileConfig::get_Value('db_type', $this->catalog_current_id);
        $user = '';
        $pwd = '';

        if ($driver != 'sqlite') {
            $user    = FileConfig::get_Value('login', $this->catalog_current_id);
            $pwd    = FileConfig::get_Value('password', $this->catalog_current_id);
        }

        switch ($driver) {
            case 'mysql':
            case 'pgsql':
                $this->db_link = CDB::connect($dsn, $user, $pwd);
                break;
            case 'sqlite':
                $this->db_link = CDB::connect($dsn);
                break;
        }
            
     // Getting driver name from PDO connection
        $this->db_driver = CDB::getDriverName();

     // Set PDO connection options
        $this->db_link->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        $this->db_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db_link->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('CDBResult', array($this)));
            
     // MySQL connection specific parameter
        if ($driver == 'mysql') {
            $this->db_link->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        }

     // Bacula catalog selection
        if ($this->catalog_nb > 1) {
            // Catalogs list
            $this->view->assign('catalogs', FileConfig::get_Catalogs());
         // Catalogs count
            $this->view->assign('catalog_nb', $this->catalog_nb);
        }
    }
                    
        // ==================================================================================
        // Function: 	GetVolumeList()
        // Parameters: 	none
        // Return:	array of volumes ordered by poolid and volume name
        // ==================================================================================

    public function GetVolumeList()
    {
        $pools = array();
        $query = "";
                
        foreach (Pools_Model::getPools($this->db_link) as $pool) {
            $pool_name = $pool['name'];
            
            switch ($this->db_driver) {
                case 'sqlite':
                case 'mysql':
                    $query  = "SELECT Media.volumename, Media.volbytes, Media.volstatus, Media.mediatype, Media.lastwritten, Media.volretention, Media.slot, Media.InChanger
									FROM Media LEFT JOIN Pool ON Media.poolid = Pool.poolid
									WHERE Media.poolid = '". $pool['poolid'] . "' ORDER BY Media.volumename";
                    break;
                case 'pgsql':
                    $query  = "SELECT media.volumename, media.volbytes, media.volstatus, media.mediatype, media.lastwritten, media.volretention, media.slot, media.inchanger
									FROM media LEFT JOIN pool ON media.poolid = pool.poolid
									WHERE media.poolid = '". $pool['poolid'] . "' ORDER BY media.volumename";
                    break;
            } // end switch

            $volumes  = CDBUtils::runQuery($query, $this->db_link);
                
            // If we have at least 1 volume in this pool, create sub array for the pool
            if (!array_key_exists($pool_name, $pools)) {
                $pools[$pool_name] = array();
                $pools[$pool_name]['volumes'] = array();
            }
                    
            foreach ($volumes->fetchAll() as $volume) {
                // Set volume default values
                $volume['expire'] = 'n/a';
                
                // Set value for unused volumes
                if (empty($volume['lastwritten'])) {
                    $volume['lastwritten'] = 'n/a';
                }else
                   $volume['lastwritten'] = date( $this->datetime_format, strtotime($volume['lastwritten']));
                
                // Get volume used bytes in a human format
                $volume['volbytes'] = CUtils::Get_Human_Size($volume['volbytes']);
                
                // If volume have already been used
                if ($volume['lastwritten'] != "0000-00-00 00:00:00") {
                    // Calculate expiration date if the volume status is Full or Used
                    if ($volume['volstatus'] == 'Full' || $volume['volstatus'] == 'Used') {
                        $expire_date = strtotime($volume['lastwritten']) + $volume['volretention'];
                        $volume['expire'] = strftime("%Y-%m-%d", $expire_date);
                    }
                }
		
		// Update volume inchanger
                if( $volume['inchanger'] == '0' ) {
 		    $volume['inchanger'] = '-'; 
		}else {
 		    $volume['inchanger'] = '<span class="glyphicon glyphicon-ok"></span>'; 
		}
                                        
            // Push the volume array to the $pool array
            array_push($pools[ $pool_name]['volumes'], $volume);
            } // end foreach volumes

            // Calculate used bytes for each pool
            $sql = "SELECT SUM(Media.volbytes) as sumbytes FROM Media WHERE Media.PoolId = '" . $pool['poolid'] . "'";
            $result = CDBUtils::runQuery($sql, $this->db_link);
            $result = $result->fetchAll();
            $pools[$pool_name]['total_used_bytes'] = CUtils::Get_Human_Size($result[0]['sumbytes']);
        } // end foreach pools

        return $pools;
    } // end function GetVolumeList()
} // end class Bweb

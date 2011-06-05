<?php
/* 
+-------------------------------------------------------------------------+
| Copyright 2010-2011, Davide Franco			                          |
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
 define( 'BW_ROOT', getcwd() );	
 define( 'BW_OBJ', BW_ROOT . '/includes/' ); 
 define( 'BW_EXTERNAL', BW_OBJ . 'external' );
 
 define( 'BW_PHPLOT', BW_EXTERNAL . '/phplot/'  );					
 define( 'BW_SMARTY', BW_EXTERNAL . '/smarty/libs/' );				
 define( 'BW_SMARTY_GETTEXT', BW_EXTERNAL . '/smarty_gettext-0.9/' );
 
 require_once( BW_SMARTY . "Smarty.class.php");			
 require_once( BW_PHPLOT . "phplot.php");				
 
 // PEAR-DB classe
 require_once "DB.php";   
 
 // Internal libs
 require_once BW_OBJ . "cfg/config.class.php";
 require_once BW_OBJ . "graph/bgraph.class.php";
 require_once BW_OBJ . "bweb.inc.php";
 
 // Global constants
 define('CONFIG_DIR', BW_ROOT . "/config/");
 define('CONFIG_FILE', CONFIG_DIR . "config.php");
 require_once( CONFIG_FILE );
 
 // Time intervals in secondes
 define( 'LAST_DAY', 86400 );
 define( 'LAST_WEEK', 604800 );
 define( 'LAST_MONTH', 2678400 );
 define( 'ALL', -1 );
 
 // Job status code
 define( 'J_NOT_RUNNING', 		  'C' );
 define( 'J_RUNNING', 			  'R' );
 define( 'J_BLOCKED', 			  'B' );
 define( 'J_COMPLETED', 		  'T' );
 define( 'J_COMPLETED_ERROR', 	  'E' );
 define( 'J_NO_FATAL_ERROR', 	  'e' );
 define( 'J_FATAL', 			  'f' );
 define( 'J_CANCELED', 			  'A' );
 define( 'J_WAITING_CLIENT', 	  'F' );
 define( 'J_WAITING_SD', 		  'S' );
 define( 'J_WAITING_NEW_MEDIA',	  'm' );
 define( 'J_WAITING_MOUNT_MEDIA', 'M' );
 define( 'J_WAITING_STORAGE_RES', 's' );
 define( 'J_WAITING_JOB_RES',     'j' );
 define( 'J_WAITING_CLIENT_RES',  'c' );
 define( 'J_WAITING_MAX_JOBS',    'd' );
 define( 'J_WAITING_START_TIME',  't' );
 define( 'J_WAITING_HIGH_PR_JOB', 'p' );
?>

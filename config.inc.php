<?php
 define( 'BW_ROOT', getcwd() );														
 define( 'BW_PHPLOT', BW_ROOT . '/external_packages/phplot/'  );					
 define( 'BW_SMARTY', BW_ROOT . '/external_packages/smarty/libs/' );				
 define( 'BW_SMARTY_GETTEXT', BW_ROOT . '/external_packages/smarty_gettext-0.9/' );
 
 require_once( BW_SMARTY . "Smarty.class.php");			
 require_once( BW_PHPLOT . "phplot.php");				
 
 // PEAR-DB classe
 require_once "DB.php";   
 
 // Internal libs
 require_once "bgraph.inc.php";
 require_once "bweb.inc.php";
 
 // Global constants
 define('CONFIG_DIR', "configs");
 define('CONFIG_FILE', "bacula.conf");
 define('BACULA_TYPE_BYTES_FILES', 1);
 define('BACULA_TYPE_FILES_JOBID', 2);
 define('BACULA_TYPE_BYTES_ENDTIME_ALLJOBS', 69);

 // Time intervals in secondes
 define( 'LAST_DAY', 86400 );
 define( 'LAST_WEEK', 604800 );
 define( 'LAST_MONTH', 2678400 );
 define( 'ALL', -1 );
?>

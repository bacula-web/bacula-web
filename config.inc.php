<?php
 // PhPlot (version 5.3.1) 
 // http://www.phplot.com
 $phplot_path = "external_packages/phplot/";

 // Smarty (version 2.6.26)
 // http://smarty.php.net
 $smarty_path = "external_packages/smarty/libs/";

 // Smarty_gettext (version 0.9)
 // http://www.boom.org.il/smarty/gettext/
 $smarty_gettext_path = "external_packages/smarty_gettext-0.9/"; 
 
 require_once( $smarty_path . "Smarty.class.php");
 require_once( $phplot_path . "phplot.php");
 
 require_once "DB.php";   
 require_once "bgraph.inc.php";
 
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

<?php
  session_start();
  require_once ("paths.php");
  require_once ($smarty_path."Smarty.class.php");
  require_once ("bweb.inc.php");
  require_once ("config.inc.php");  

  $smarty = new Smarty();     
  $dbSql = new Bweb();

  require("lang.php");

  // Smarty configuration
  $smarty->compile_check = true;
  $smarty->debugging = false;
  $smarty->force_compile = true;

  $smarty->template_dir = "./templates";
  $smarty->compile_dir = "./templates_c";
  $smarty->config_dir     = "./configs";

  $backupjob_name = "";
  
  if( isset( $_POST["backupjob_name"] ) )
    $backupjob_name = $_POST["backupjob_name"];
  elseif( isset( $_GET["backupjob_name"] ) )
	$backupjob_name = $_GET["backupjob_name"];
  else
	die( "Please specify a backup job name " );
	
  $smarty->assign('backupjob_name', $backupjob_name );
	
  // Last 7 days stored Bytes graph
  $data  = array();
  $graph = new BGraph( "graph2.png" );
  $days  = array();

  // Get the last 7 days interval (start and end)
  for( $c = 6 ; $c >= 0 ; $c-- ) {
	  $today = ( mktime() - ($c * LAST_DAY) );
	  array_push( $days, array( 'start' => date( "Y-m-d 00:00:00", $today ), 'end' => date( "Y-m-d 23:59:00", $today ) ) );
  }

  $days_stored_bytes = array();

  foreach( $days as $day ) {
    array_push( $days_stored_bytes, $dbSql->GetStoredBytesByJob( $backupjob_name, $day['start'], $day['end'] ) );
  }

  $graph->SetData( $days_stored_bytes, 'bars', 'text-data' );
  $graph->SetGraphSize( 400, 230 );

  $graph->Render();
  $smarty->assign('graph_stored_bytes', $graph->Get_Image_file() );	
  
  
  // Process and display the template 
  $smarty->display('backupjob-report.tpl'); 
  
?>
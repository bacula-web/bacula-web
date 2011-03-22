<?php
  session_start();
  include_once( 'config.inc.php' );

  $dbSql = new Bweb();

  // Get volumes list (pools.tpl)
  $dbSql->tpl->assign('pools',$dbSql->GetVolumeList() );

  $dbSql->tpl->display('pools.tpl');
?>

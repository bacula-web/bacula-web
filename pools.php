<?php
  session_start();
  include_once( 'bweb.inc.php' );

  $dbSql = new Bweb();

  // Get volumes list (pools.tpl)
  $dbSql->tpl->assign('pools',$dbSql->GetVolumeList() );

  $dbSql->tpl->display('pools.tpl');
?>

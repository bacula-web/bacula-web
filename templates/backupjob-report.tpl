<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>bacula-web</title>
<link rel="stylesheet" type="text/css" href="style/default.css">
{literal}
<script type="text/javascript">
	function OpenWin(URL,wid,hei) {
		window.open(URL,"window1","width="+wid+",height="+hei+",scrollbars=yes,menubar=no,location=no,resizable=no")
	}
</script>
{/literal}

</head>
<body>
{popup_init src='./external_packages/js/overlib.js'}
{include file=header.tpl}

  <div id="nav">
    <a href="index.php" title="Back to the dashboard">Dashboard</a> > Backup Job Report
  </div>

  <div id="main_center">
  
  <div class="box">
	<p class="title">Backup Job Report</p>
	
	<table>
		<tr>
			<td width="150">Backup Job name:</td>
			<td>{$backupjob_name}</td>
		</tr>
		<tr>
			<td>Period:</td>
			<td>ppp{$backupjob_period}</td>
		</tr>
		<tr>
			<td>Transfered Bytes</td>
			<td>{$backupjob_bytes} GB</td>
		</tr>
		<tr>
			<td>Transfered Files</td>
			<td>ppp{$backupjob_files}</td>
		</tr>

	</table>
  </div> <!-- end div class=box -->
  
  <!-- Last jobs list -->
  <div class="box">
	<p class="title">Last jobs</p>
  </div> <!-- end div class=box -->
  
  <!-- Transfered Bytes graph -->
  <div class="box">
	<p class="title">Transfered Bytes (last week in GB)</p>
	<img src="{$graph_stored_bytes}" alt="" />
  </div> <!-- end div class=box -->

  <!-- Transfered Files graph -->
  <div class="box">
	<p class="title">Transfered Files (last week in GB)</p>
	<img src="{$graph_stored_files}" alt="" />
  </div> <!-- end div class=box -->
  
  </div> <!-- end div id=main_center -->

{include file="footer.tpl"}
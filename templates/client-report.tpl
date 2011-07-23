<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>bacula-web</title>
<link rel="stylesheet" type="text/css" href="style/default.css">
</head>
<body>
{include file=header.tpl}

  <div id="nav">
    <a href="index.php" title="Back to the dashboard">Dashboard</a> > Client(s) report
  </div>

  <div id="main_center">
  
  <div class="box">
	<p class="title">Client(s) Report</p>

  <h4>Client informations</h4>	
  <table width="300px">
	<tr>
		<td width="100px" class="label"><b>Client name:</b></td> <td>{$client_name}</td>
	</tr>
	<tr>
		<td class="label"><b>Client version:</b></td> <td>{$client_version}</td>
	</tr>
	<tr>
		<td class="label"><b>Client os:</b></td> <td>{$client_os}</td>
	</tr>
	<tr>
		<td class="label"><b>Client arch:</b></td> <td>{$client_arch}</td>
	</tr>
  </table>
	
  </div> <!-- end div class=box -->
  
  </div> <!-- end div id=main_center -->

{include file="footer.tpl"}

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
    <a href="index.php" title="Back to the dashboard">Dashboard</a> > Test page
  </div>
<div id="main_center">
	<div class="box">
	  <p class="title">Required components</p>
	<table>
		{foreach from=$checks item=check}
		  <tr>
		    <td> <b>{$check.check_label}</b> </td>
			<td>{$check.check_descr}</td>
			<td> <img src='style/images/{$check.check_result}' width='20' alt=''/></td>
		  </tr>
		{/foreach}
	</table>
	</div> <!-- end div class=box -->
	
	<div class="box">
	  <p class="title">Graph</p>
	<table>
	  <tr>
	    <td>
			<b>Graph system capabilities (Bacula-web only use PNG)</b>
		</td>
	    <td colspan="2">
		  <img src="{$graph_test}" alt='' />
	    </td>
	  </tr>
	</table>
	</div> <!-- end div class=box -->
</div> <!-- end div id=main_center -->

</body>
</html>

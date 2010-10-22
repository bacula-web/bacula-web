<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
{*{popup_init src="js/overlib.js"}*}
{literal}
<script language="Javascript">
	function OpenWin(URL,wid,hei) {
		window.open(URL,"window1","width="+wid+",height="+hei+",scrollbars=yes,menubar=no,location=no,resizable=no")
	}
</script>
{/literal}
<link rel="stylesheet" type="text/css" href="style/default.css" />
<title>bacula-web</title>
</head>
<body>

{include file=header.tpl}

{config_load file=bacula.conf}

<table width=1000px border=0 cellspacing=5 class="back">
  <tr>
	<td valign=top width=60%> 
	  {include file=generaldata.tpl} 
	  <br /> 
	  {include file=volumes.tpl}
	</td>
	<td valign=top width=40% bgcolor=#DDDFF9 style="border-style: solid; border-color: grey">
  	  {if !#IndexReport#}
	    {include file=last_run_report.tpl} 	
	  {else}
		{include file=report_select.tpl}
	  {/if}
	  <table class=genmed cellspacing="1" cellpadding="3" border=0 align="center">
		<tr>
		  <td>
			{if $server==""}
			  <img src="stats.php?server={$server}&tipo_dato=69&title=General%20report&modo_graph=bars&sizex=420&sizey=250&MBottom=20&legend=1" />
			{else}
			  <img src="stats.php?server={$server}&tipo_dato=3&title={$server}&modo_graph=bars" />
			{/if}
		  </td>
		</tr>
	  </table>
	</td>
  </tr>
</table>

{include file="footer.tpl"}
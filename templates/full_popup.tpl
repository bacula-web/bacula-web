{config_load file=bacula.conf}
<html>
<head>
{include file=css.tpl}
<title>Popup</title></head>
<body bgcolor="#FBF7CE" topmargin=0 bottommargin=0 leftmargin=0 rightmargin=0 marginwidth=0 marginheight=0>
{if $smarty.get.Full_popup == "yes"}
	{include file=last_run_report.tpl}
{elseif $smarty.get.pop_graph1 == "yes"}
	<img src="stats.php?tipo_dato=69&title={t}Bytes transferred last 30 days from ALL clients{/t}&modo_graph=lines&sizex=600&sizey=400&MBottom=80&legend=1&elapsed=2592000">
{elseif $smarty.get.pop_graph2 == "yes"}
	<img src="stats.php?tipo_dato=69&title={t}Bytes transferred last 30 days from ALL clients{/t}&modo_graph=pie&sizex=600&sizey=400&MBottom=80&legend=1&elapsed=2592000">
{/if}
{include file=footer.tpl}
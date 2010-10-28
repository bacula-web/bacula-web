{* Página principal BACULA *}
{config_load  file=bacula.conf}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>{t}Stats Bacula: Job{/t} {$smarty.get.server}</title>
<link rel="stylesheet" type="text/css" href="style/default.css" />
</head>
<body>
{popup_init src="js/overlib.js"}
<table width=100% border=1 class=back>
<tr>
	<td width=65% valign=top>
		<table width=100% cellpadding=5>
		<tr>
			<td align=center class=titulo>
				{t}Job:{/t} <font color=blue> {$smarty.get.server}</font>
				<br>
				{t}Period: From{/t} <font color=red>{$startperiod|date_format:"%d/%m/%Y"}</font> {t}to{/t} <font color=red>{$endperiod|date_format:"%d/%m/%Y"}</font>
			</td>
		</tr>

		<tr>
			<td align=left>
				<table width=100%>
				<tr>
					<td class=table1>
						{t}Bytes transferred in the period:{/t}
					</td>
					<td align=right width=15% class=table2>
						{$bytesperiod}
					</td>
				</tr>
				<tr>
					<td class=table1 >
						{t}Files transferred in the period:{/t}
					</td>
					<td align=right class=table2>
						{$filesperiod}
					</td>
				</tr>
				<tr>
					<td colspan=2>
						{if $smarty.get.server==""}
							<img src=stats.php?server={$smarty.get.server|escape:"url"}&amp;tipo_dato=69&amp;title=Análisis%20general&amp;modo_graph=lines>
						{elseif $smarty.get.default == 1}
							<img src=stats.php?server={$smarty.get.server|escape:"url"}&amp;tipo_dato=3&amp;title={$smarty.get.server|escape:"url"}&amp;modo_graph=bars&amp;StartDateMonth={$startperiod|date_format:"%m"}&amp;StartDateDay={$startperiod|date_format:"%d"}&amp;StartDateYear={$startperiod|date_format:"%Y"}&amp;EndDateMonth={$endperiod|date_format:"%m"}&amp;EndDateDay={$endperiod|date_format:"%d"}&amp;EndDateYear={$endperiod|date_format:"%Y"}>						
						{else}
							<img src=stats.php?server={$smarty.get.server|escape:"url"}&amp;tipo_dato={$smarty.get.tipo_dato}&amp;title={$smarty.get.server|escape:"url"}&amp;modo_graph={$smarty.get.modo_graph}&amp;StartDateMonth={$smarty.get.StartDateMonth}&StartDateDay={$smarty.get.StartDateDay}&StartDateYear={$smarty.get.StartDateYear}&EndDateMonth={$smarty.get.EndDateMonth}&EndDateDay={$smarty.get.EndDateDay}&EndDateYear={$smarty.get.EndDateYear}>
						{/if}
					</td>
				</tr>		
				</table>
			</td>
		</tr>
		</table>	
	</td>
	<td width=35%>
		<table width=100% border=0>
		<tr>
			<td>{include file=report_select.tpl}</td>
		</tr>
		<tr>
			<td>  
				<table width=100% border=0 class=genmed cellpadding=0 cellspacing=2>
				<tr class=titulo2>
					<td style="background-image: url('style/images/bg7.gif'); height: 25px;">{t}JobID{/t}</td>
					<td style="background-image: url('style/images/bg7.gif'); height: 25px;">{t}Date{/t}</td>
					<td style="background-image: url('style/images/bg7.gif'); height: 25px;">{t}Elapsed{/t}</td>
					<td style="background-image: url('style/images/bg7.gif'); height: 25px;">{t}Level{/t}</td>
					<td style="background-image: url('style/images/bg7.gif'); height: 25px;">{t}Bytes{/t}</td>
					<td style="background-image: url('style/images/bg7.gif'); height: 25px;">{t}Status{/t}</td>
				</tr>
				{section name=job loop=$jobs}
				<tr class={cycle values="table1,table2"}>
					<td align=center>{$jobs[job].JobId}</td>
				  	<td {popup caption="Sheduled time" text=$jobs[job].SchedTime}>{$jobs[job].StartTime}</td>
				  	<td {popup autostatus=yes caption="EndTime" text=$jobs[job].EndTime}>{$jobs[job].elapsed}</td>
					<td align=center>{$jobs[job].Level}</td>
				  	<td align=right>{$jobs[job].JobBytes}</td>
				  	<td align=center width=1%>
					  	{if $jobs[job].JobStatus eq "T"}
						  	<img src=style/images/s_ok.gif>
					  	{else}
							<img src=style/images/s_error.gif>
						{/if}
					</td>
				</tr>
				{/section}
				</table>
			</td>
		</tr>
		</table>
	<td>
</tr>
</table>	



{include file="footer.tpl"}

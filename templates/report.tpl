{* Página principal BACULA *}
{config_load  file=bacula.conf}
{popup_init src="js/overlib.js"}
<html>
<head>

<title>{t}Stats Bacula: Job{/t} {$smarty.get.server}</title></head>
<body>

{include file=css.tpl}

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
						{$bytesperiod|fsize_format}
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
							<img src=stats.php?server={$smarty.get.server|escape:"url"}&tipo_dato=69&title=Análisis%20general&modo_graph=lines>
						{elseif $smarty.get.default == 1}
							<img src=stats.php?server={$smarty.get.server|escape:"url"}&tipo_dato=3&title={$smarty.get.server|escape:"url"}&modo_graph=bars&StartDateMonth={$startperiod|date_format:"%m"}&StartDateDay={$startperiod|date_format:"%d"}&StartDateYear={$startperiod|date_format:"%Y"}&EndDateMonth={$endperiod|date_format:"%m"}&EndDateDay={$endperiod|date_format:"%d"}&EndDateYear={$endperiod|date_format:"%Y"}>						
						{else}
							<img src=stats.php?server={$smarty.get.server|escape:"url"}&tipo_dato={$smarty.get.tipo_dato}&title={$smarty.get.server|escape:"url"}&modo_graph={$smarty.get.modo_graph}&StartDateMonth={$smarty.get.StartDateMonth}&StartDateDay={$smarty.get.StartDateDay}&StartDateYear={$smarty.get.StartDateYear}&EndDateMonth={$smarty.get.EndDateMonth}&EndDateDay={$smarty.get.EndDateDay}&EndDateYear={$smarty.get.EndDateYear}>
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
					<td background="images/bg7.gif" height=25>{t}JobID{/t}</td>
					<td background="images/bg7.gif" height=25>{t}Date{/t}</td>
					<td background="images/bg7.gif" height=25>{t}Elapsed{/t}</td>
					<td background="images/bg7.gif" height=25>{t}Level{/t}</td>
					<td background="images/bg7.gif" height=25 align=center>{t}Bytes{/t}</td>
					<td background="images/bg7.gif" height=25 width=1%>{t}Status{/t}</td>
				</tr>
				{section name=job loop=$jobs}
				<tr class={cycle values="table1,table2"}>
					<td align=center>{$jobs[job].JobId}</td>
				  	<td {popup caption="Sheduled time" text=$jobs[job].SchedTime}>{$jobs[job].StartTime}</td>
				  	<td {popup autostatus=yes caption="EndTime" text=$jobs[job].EndTime}>{$jobs[job].elapsed}</td>
					<td align=center>{$jobs[job].Level}</td>
				  	<td align=right>{$jobs[job].JobBytes|fsize_format}</td>
				  	<td align=center width=1%>
					  	{if $jobs[job].JobStatus eq "T"}
						  	<img src={#root#}/images/s_ok.gif>
					  	{else}
							<img src={#root#}/images/s_error.gif>
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

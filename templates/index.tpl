{* BACULA main page*}

{config_load file=bacula.conf}

{include file=css.tpl}

{include file=header.tpl}

<table width=1000px border=0 cellspacing=5 class=back>
	<tr><td valign=top width=60%> {include file=generaldata.tpl} <br> {include file=volumes.tpl}</td>
		<td valign=top width=40% bgcolor=#DDDFF9 style="border-style: solid; border-color: grey">
			{if !#IndexReport#}
				{include file=last_run_report.tpl} 	
			{else}
				{include file=report_select.tpl}
			{/if}
			<table class=genmed cellspacing="1" cellpadding="3" border=0 align="center">
				<tr><td>
						{if $server==""}
							<img src=stats.php?server={$server}&tipo_dato=69&title={t}General%20report{/t}&modo_graph=bars&sizex=420&sizey=250&MBottom=20&legend=1>
						{else}
							<img src=stats.php?server={$server}&tipo_dato=3&title={$server}&modo_graph=bars>
						{/if}
				</td></tr>
			</table>
		</td>
	</tr>

</table>


{include file="footer.tpl"}
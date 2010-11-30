<!-- last_run_report.tpl -->

<div class="box">
	<p class="title">Last 24 hours status</p>
{* {if $mode == "Lite" && $smarty.get.Full_popup != "yes"} *}
<table>
 <tr>
 	<td class="label">Failed jobs</td> <td class="info">{$failed_jobs}</td>
 </tr>
 <tr>
 	<td class="label">Completed jobs</td> <td class="info">{$completed_jobs}</td>
 </tr> 
 <tr>
 	<td class="label">Elapsed time</td> <td class="info">{$elapsed_jobs}</td>
 </tr>
 <tr>
 	<td class="label">Transferred Bytes</td> <td class="info">{$bytes_totales}</td>
 </tr> 
 <tr>
 	<td colspan=2 align=center>
 		<!-- <a href="javascript:OpenWin('index.php?Full_popup=yes','490','350')">{t}Show details{/t}</a> -->
		<a href="jobs.php" title="Show last jobs details">Show last jobs status</a>
 	</td>
 </tr>
</table>

</div> <!-- end div box --> 
<!--
{* {if $status != 0} *}
<table>
	<tr>
 		<td colspan=2>
 			<table border=0 cellpadding=0 cellspacing=0>
 				<tr>
 					<td align=right colspan=4 height=25 background="images/bg7.gif" style="font-size: 12px">
 						<b>{t}Jobs with errors{/t}</b>
 					</td>
 				</tr>
 				<tr>
 					<td background="style/images/bg6.png"><b>JobId&nbsp;</b></td>
 					<td background="style/images/bg6.png"><b>{t}Name{/t}</b></td>
 					<td background="style/images/bg6.png"><b>{t}EndTime{/t}</b></td>
 					<td background="style/images/bg6.png"><b>{t}JobStatus{/t}</b></td>
 				</tr>
 				{section name=row loop=$errors_array} 
 				<tr {* bgcolor=#{cycle values="E6E6F5,E1E5E0"} *}>
 					{section name=tmp loop=$errors_array[row]}
 					<td {if $smarty.section.tmp.iteration == 4}align=center 
                        	{if $errors_array[row][tmp] == "C"}
                        		{assign var=pop value="Created but not yet running"}
                        	{elseif $errors_array[row][tmp] == "R"}
                        		{assign var=pop value="Running"}
                        	{elseif $errors_array[row][tmp] == "B"}
                        		{assign var=pop value="Blocked"}
                        	{elseif $errors_array[row][tmp] == "E"}
                        		{assign var=pop value="Terminated in Error"}
                        	{elseif $errors_array[row][tmp] == "e"}
                        		{assign var=pop value="Non-fatal error"}
                        	{elseif $errors_array[row][tmp] == "f"}
                        		{assign var=pop value="Fatal error"}
                        	{elseif $errors_array[row][tmp] == "D"}
                        		{assign var=pop value="Verify Differences"}
                        	{elseif $errors_array[row][tmp] == "A"}
                        		{assign var=pop value="Canceled by the user"}
                        	{elseif $errors_array[row][tmp] == "F"}
                        		{assign var=pop value="Waiting on the File daemon"}
                        	{elseif $errors_array[row][tmp] == "S"}
                        		{assign var=pop value="Waiting on the Storage daemon"}
                        	{elseif $errors_array[row][tmp] == "m"}
                        		{assign var=pop value="Waiting for a new Volume to be mounted"}
                        	{elseif $errors_array[row][tmp] == "M"}
                        		{assign var=pop value="Waiting for a Mount"}
                        	{elseif $errors_array[row][tmp] == "s"}
                        		{assign var=pop value="Waiting for Storage resource"}
                        	{elseif $errors_array[row][tmp] == "j"}
                        		{assign var=pop value="Waiting for Job resource"}
                        	{elseif $errors_array[row][tmp] == "c"}
                        		{assign var=pop value="Waiting for Client resource"}
                        	{elseif $errors_array[row][tmp] == "d"}
                        		{assign var=pop value="Wating for Maximum jobs"} 
                        	{elseif $errors_array[row][tmp] == "t"}
                        		{assign var=pop value="Waiting for Start Time"}
                        	{elseif $errors_array[row][tmp] == "p"}
                        		{assign var=pop value="Waiting for higher priority job to finish"}
                        	{/if}
                        {popup caption="Status detail" autostatus=yes fgcolor=red textcolor=yellow text="$pop"}
 						{/if}  					
 					>
 						{if $smarty.section.tmp.iteration == 2}
                            <a href=report.php?default=1&server={$errors_array[row][tmp]} target="_blank">
                        {/if}    
 						{$errors_array[row][tmp]}
 						{if $smarty.section.row.iteration == 2}
 							</a>
 						{/if}
 					</td>
 					{/section}
 				</tr>
 				{/section}
 			</table>
 		</td>
 	</tr>
</table>	
 {* {/if} *}
</div> 
-->
<div class="box">
	<p class="title">Detailled report</p>
 
 <form method="get" action="report.php" target="_blank">
   <table width="100%" cellpadding="0" cellspacing="3" border="0">
	 <tr>
 	   <td class="label">{t}Select a job:{/t}</td>
 	   <td align=center>
	     <input type=hidden name="default" value="1"> 				
		   <select name=server>
 		     {if $smarty.get.server != ""}
 				{html_options values=$smarty.get.server output=$smarty.get.server}
 			 {else}
 		   	    {html_options values=$total_name_jobs output=$total_name_jobs}
 		     {/if}
 		   </select>
 	     <input type=submit value="{t}go{/t}">
	   </td>
     </tr>
   </table>
 </form>
</div>
<!--
{* {else if #mode# == "Full" || $smarty.get.Full_popup == "yes"} *}
<table>
	<tr class="tbl_header1">
 		<td><b>{t}Elapsed time{/t}</b></td>
		<td><b>{t}Client{/t}</b></td>
 		<td><b>{t}Start Time{/t}</b></td>
 		<td><b>{t}End Time{/t}</b></td>
 		<td><b>{t}Type{/t}</b></td>
 		<td><b>{t}Pool{/t}</b></td>
 		<td><b>{t}Status{/t}</b></td>
 	</tr>
 	{section name=job loop=$clients}
 	<tr class={cycle values="table3,table4"}>
 		{section name=row loop=$clients[job]}
			<td align=left class="size_small">
			{if $smarty.section.row.iteration == 2}
 				<a href=report.php?default=1&server={$clients[job][row]|escape:"url"} target="_blank">
 			{/if}
 			{if $smarty.section.row.last == TRUE}
			{if $clients[job][row] eq "T"}
			  	<img src={#root#}/style/images/s_ok.gif>
			{else}
				<img src={#root#}/style/images/s_error.gif>
			{/if}
			{else}
				{$clients[job][row]}
			{/if}
				{if $smarty.section.row.iteration == 2}
			</a>
			{/if}
			</td>
		{/section}
 	</tr>
 		{/section}
 	</td>
 </tr>
</table>
{* {/if} *}

</div>
-->

<!-- end last_run_report.tpl -->
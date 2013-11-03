{include file=header.tpl}

<div id="main_center">
  <div class="box">
    <h4>{t}Backup job informations{/t}</h4>	

    <table class="table_big">
		<tr>
			<td>{t}Backup Job name{/t}:</td> <td class="strong">{$backupjob_name}</td>
		</tr>
		<tr>
			<td>{t}Period{/t}:</td> <td class="strong">{$backupjob_period}</td>
		</tr>
		<tr>
			<td>{t}Transfered Bytes{/t}</td> <td class="strong">{$backupjob_bytes}</td>
		</tr>
		<tr>
			<td>{t}Transfered Files{/t}</td> <td class="strong">{$backupjob_files}</td>
		</tr>
      </table>
  
  <!-- Last jobs list -->
  <h4>{t}Last jobs{/t}</h4>
	<table>
		<tr>
			<th>{t}Job Id{/t}</th>
			<th>{t}Level{/t}</th>
			<th>{t}Files{/t}</th>
			<th>{t}Bytes{/t}</th>
			<th>{t}Start time{/t}</th>
			<th>{t}End time{/t}</th>
			<th>{t}Elapsed time{/t}</th>
			<th>{t}Speed{/t}</th>
		</tr>
		{foreach from=$jobs item=job}
		<tr class="{$job.odd_even}">
			<td>{$job.jobid}</td>
			<td>{$job.joblevel}</td>
			<td>{$job.jobfiles}</td>
			<td>{$job.jobbytes}</td>
			<td>{$job.starttime}</td>
			<td>{$job.endtime}</td>
			<td>{$job.elapsedtime}</td>
			<td>{$job.speed}</td>
		</tr>
		{/foreach}
	</table>
  
    <!-- Transfered Bytes/Files graph -->
	<h4>{t}Transfered Bytes / Files{/t}</h4>
    <div class="box">
	  <img class="graph" src="{$graph_stored_bytes}" alt="" />
	  <img class="graph" src="{$graph_stored_files}" alt="" />
    </div> <!-- end div class=box -->
</div> <!-- end div id=main_center -->

{include file="footer.tpl"}

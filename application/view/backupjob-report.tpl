{include file=header.tpl}

<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-lg-8">
      <h4>{t}Backup job informations{/t}</h4>	

      <table class="table table-condensed table-bordered">
	<tr>
	  <td>{t}Backup Job name{/t}:</td> <td>{$backupjob_name}</td>
	</tr>
	<tr>
	  <td>{t}Period{/t}:</td> <td>{$backupjob_period}</td>
	</tr>
	<tr>
	  <td>{t}Transfered Bytes{/t}</td> <td>{$backupjob_bytes}</td>
	</tr>
	<tr>
	  <td>{t}Transfered Files{/t}</td> <td>{$backupjob_files}</td>
	</tr>
      </table>
  
  <!-- Last jobs list -->
  <h4>{t}Last jobs{/t}</h4>
	<table class="table table-condensed table-hover table-striped table-bordered text-center">
		<tr>
			<th class="text-center">{t}Job Id{/t}</th>
			<th class="text-center">{t}Level{/t}</th>
			<th class="text-center">{t}Files{/t}</th>
			<th class="text-center">{t}Bytes{/t}</th>
			<th class="text-center">{t}Start time{/t}</th>
			<th class="text-center">{t}End time{/t}</th>
			<th class="text-center">{t}Elapsed time{/t}</th>
			<th class="text-center">{t}Speed{/t}</th>
                        <th class="text-center">{t}Compression{/t}</th>
		</tr>
		{foreach from=$jobs item=job}
		<tr> 
			<td>{$job.jobid}</td>
			<td>{$job.joblevel}</td>
			<td>{$job.jobfiles}</td>
			<td>{$job.jobbytes}</td>
			<td>{$job.starttime}</td>
			<td>{$job.endtime}</td>
			<td>{$job.elapsedtime}</td>
			<td>{$job.speed}</td>
                        <td>{$job.compression}</td>
		</tr>
		{/foreach}
	</table>
  
    </div> <!-- div class="col-md-..." -->
   </div> <!-- div class="row" -->


   <!-- Transfered Bytes/Files graph -->
   <h4>{t}Transfered Bytes / Files{/t}</h4>

   <div class="row">
     <div class="col-md-4 col-lg-4">
       <img class="img-responsive" src="{$graph_stored_bytes}" alt="" />
     </div>
     <div class="col-md-4 col-lg-4">
       <img class="img-responsive" src="{$graph_stored_files}" alt="" />
     </div>

  </div> <! -- div class="row" -->
</div> <!-- class="container-fluid" -->

{include file="footer.tpl"}

<div class="container-fluid">
   <div class="page-header">
     <h3>{$page_name} <small>{t}General overview{/t}</small></h3>
   </div>
	
	<!-- First row with Jobs statistics, stored bytes and stored files widgets -->
	<div class="row">
		<!-- Last period job status -->
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading"> <b>{t}Last period job status{/t}</b> ({$literal_period}) </div>
				<!-- Period selector -->
					<div class="panel-body">
						<form class="form-inline pull-right" method="post" role="form" action="index.php">
						    <div class="form-group form-group-sm">
                            	<label class="control-label">{t}Period{/t} </label>
                                <select class="form-control input-sm" name="period_selector">
                                {foreach from=$custom_period_list key=period_id item=period_label}
                                	<option value="{$period_id}"
                                    {if $period_id eq $custom_period_list_selected} selected {/if}>{$period_label}
                                    </option>
                                {/foreach}
                           		</select>
                                <button title="{t}Update with selected period{/t}" class="btn btn-default btn-sm" type="submit">{t}Submit{/t}</button>
						    </div> <!-- div class="form-group"-->
                        </form> 
					</div> <!-- end div class="panel-body" --> 

					<!-- Last period job status graph -->
					<div class="panel-body">
                  <div id="{$last_jobs_chart_id}"> <svg></svg> </div>
                     {$last_jobs_chart}

						<table class="table table-condensed">
							<tr>
								<td><h5>{t}Running jobs{/t}</h5></td>
								<td class="text-center"> <a href="index.php?page=jobs&filter_jobstatus=1"><h4><span class="label label-default">{$running_jobs}</span></h4></a> </td>
							</tr>
							<tr>
 								<td><h5>{t}Completed job(s){/t}</h5></td>
								<td class="text-center"> <a href="index.php?page=jobs&filter_jobstatus=3"><h4><span class="label label-success">{$completed_jobs}</span></h4></a> </td>
							</tr>
                     <tr>
                        <td><h5>{t}Completed with errors job(s){/t}
								<td class="text-center"> <a href="index.php?page=jobs&filter_jobstatus=4"><h4><span style="background-color: #FFD700;" class="label label-default">{$completed_with_errors_jobs}</span></h4></a> </td>
                     </tr>
 							<tr>
                            	<td> <h5>{t}Waiting jobs(s){/t}</h5></td>
                                <td class="text-center"> <a href="index.php?page=jobs&filter_jobstatus=2"><h4><span class="label label-primary">{$waiting_jobs}</span></h4></a> </td>
                            </tr>
							<tr>
                            	<td> <h5>{t}Failed job(s){/t}</h5></td>
                                <td class="text-center"> <a href="index.php?page=jobs&filter_jobstatus=5"><h4><span class="label label-danger">{$failed_jobs}</span></h4></a> </td>
                            </tr>
							<tr>
                            	<td> <h5>{t}Canceled job(s){/t}</h5></td>
                                <td class="text-center"> <a href="index.php?page=jobs&filter_jobstatus=6"><h4><span class="label label-warning">{$canceled_jobs}</span></h4></a> </td>
                            </tr>
							<tr>
								<td> <h5>{t}Job Level (Incr / Diff / Full){/t}</h5> </td>
								<td class="text-center"> <h4>{$incr_jobs} / {$diff_jobs} / {$full_jobs} </h4> </td>
							</tr>
							<tr> 
								<td> <h5>{t}Transferred Bytes / Files{/t}</h5> </td>
								<td class="text-center"> <h4>{$bytes_last} / {$files_last} </h4> </td>
							</tr>
						</table>
					</div> <!-- div class="panel-body" -->
				</div> <!-- div class="panel panel-default" -->
			</div> <!-- end column -->
			
			<!-- Stored Bytes for last 7 days -->
			<div class="col-xs-12 col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading" title="{t}Stored bytes over the last 7 days{/t}"><b>{t}Stored Bytes (last 7 days){/t}</b></div>
					<div class="panel-body">
                  <div id="{$storedbytes_chart_id}"> <svg></svg> </div>
                  {$storedbytes_chart}
					</div> <!-- end <div class="panel-body"> -->
				</div> <!-- end class="panel panel-default" -->
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading" title="{t}Stored files over the last 7 days{/t}"><b>{t}Stored Files (last 7 days){/t}</b></div>
					<div class="panel-body">
                  <div id="{$storedfiles_chart_id}"> <svg></svg> </div>
                  {$storedfiles_chart}
					</div>
				</div> <!-- div class="panel panel-default" -->
			</div>
		</div> <!-- div class="col-md-5 ..." -->	
	 <!-- end <div class="row"> -->

	<!-- Third row with Pools and volumes status + Last used volumes widgets -->
	<div class="row">
		<!-- Pools and volumes status -->
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Pools and volumes status{/t}</b></div>
				<div class="panel-body">
               <div id="{$pools_usage_chart_id}"> <svg></svg> </div>
               {$pools_usage_chart}
				</div>
			</div> <!-- div class="panel panel-default" -->
		</div> <!-- end class="col-xs-12 col-md-6" -->
		
		<div class="col-xs-12 col-md-6">
			<!-- Last used volumes -->
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Last used volumes{/t}</b>
                  <span class="glyphicon glyphicon-info-sign pull-right" aria-hidden="true" data-toggle="tooltip" data-placement="auto" 
                    data-original-title="{t}Displays the last 10 volumes used during backups{/t}">
                  </span>
                </div>
				<div class="panel-body">
					<div class="table-responsive">
					<table class="table table-condensed table-striped">
						<tr>
							<th title="{t}Volume name{/t}">Volume</th>
							<th title="{t}Volume status{/t}">Status</th>
							<th title="{t}Volume pool{/t}">Pool</th>
							<th title="{t}Last written date for this volume{/t}">Last written</th>
							<th title="{t}Number of jobs{/t}">Jobs</th>
						</tr>
						
						{foreach from=$volumes_list item=vol} 
							<tr>
								<td>{$vol.volumename}</td>
								<td>{$vol.volstatus}</td>
								<td>{$vol.poolname}</td>
								<td>{$vol.lastwritten}</td>
								<td class="strong">{$vol.voljobs}</td>
							</tr>
						{/foreach} 
					</table>
					</div>
				</div> <!-- <div class="panel-body"> -->
			</div> <!-- <div class="panel panel-default"> -->		

		</div> <!-- end class="col-..."-->
	</div> <!-- end <div class="row"> -->

	<div class="row">
		<div class="col-xs-12 col-md-6">

         <!-- Clients jobs total widget -->
         <div class="panel panel-default">
            <div class="panel-heading"><b>{t}Clients jobs total{/t}</b></div>
            <div class="panel-body">
             <p>{t}Per job name backup and restore jobs statistics{/t}</p>
            </div> <!-- end div class=panel-body -->
            <table class="table table-condensed">
               <tr>
                  <th>{t}Job name{/t}</th>
                  <th>{t}Jobs{/t}</th>
                  <th>{t}Files{/t}</th>
                  <th>{t}Bytes{/t}</th>
                  <th>{t}Type{/t}</th>
               </tr>
               {foreach from=$jobnames_jobs_stats item=jobname}
               <tr>
                  <td>{$jobname.jobname}</td>
                  <td>{$jobname.jobscount}</td>
                  <td>{$jobname.jobfiles}</td>
                  <td>{$jobname.jobbytes}</td>
                  <td>{$jobname.type}</td>
               </tr>
               {/foreach}
            </table>
            <div class="panel-body">
               <p>Per job type backup and restore jobs statistics</p>
            </div> <!-- end div class=panel-body -->
            <table class="table table-condensed">
               <tr>
                  <th>{t}Type{/t}</th>
                  <th>{t}Files{/t}</th>
                  <th>{t}Bytes{/t}</th>
                  <th>{t}Jobs{/t}</th>
               </tr>
               {foreach from=$jobtypes_jobs_stats item=jobtype}
               <tr>
                  <td>{$jobtype.type}</td>
                  <td>{$jobtype.jobfiles}</td>
                  <td>{$jobtype.jobbytes}</td>
                  <td>{$jobtype.jobscount}</td>
               </tr>
               {/foreach}
            </table>
         </div> <!-- end div class=panel... -->
      </div> <!-- end div class=col-xx -->
   
      <!-- Weekly jobs statistics -->
      <div class="col col-xs-12 col-md-6"> 
         <div class="panel panel-default">
            <div class="panel-heading"><b>{t}Weekly jobs statistics{/t}</b></div>
            <div class="panel-body">
               <table class="table table-condensed table-striped">
                  <tr>
                     <th>{t}Day of week{/t}</th>
                     <th>{t}Bytes{/t}</th>
                     <th>{t}Files{/t}</th>
                  </tr>
                  {foreach from=$weeklyjobsstats item=day}
                  <tr>
                     <td>{$day.dayofweek}</td>
                     <td>{$day.jobbytes}</td>
                     <td>{$day.jobfiles}</td>
                  </tr>
                  {foreachelse}
                     <tr> <td colspan="3" class="text-center">{t}Nothing to display{/t}</td> </tr>
                  {/foreach}
               </table>
            </div> <!-- end div class=panel-body -->
         </div> <!-- end div class=panel -->
      </div> <!-- end div class=col col-xx -->
   </div> <!-- end div class=row -->

   <div class="row">
      <div class="col col-xs-12 col-md-6">
         <!-- 10th biggest job names -->
         <div class="panel panel-default">
            <div class="panel panel-heading">
              <b>{t}Biggest backup jobs{/t}</b>
              <span class="glyphicon glyphicon-info-sign pull-right" aria-hidden="true" data-toggle="tooltip" data-placement="auto" 
                data-original-title="{t}Displays the 10 biggest (Bytes) Bacula backup jobs{/t}">
              </span>
            </div>
            <div class="panel-body">
               <table class="table table-condensed table-striped">
                  <tr>
                     <th>{t}Job name{/t}</th>
                     <th>{t}Bytes{/t}</th>
                     <th>{t}Files{/t}</th>
                  </tr>
                  {foreach from=$biggestjobs item=job}
                     <tr>
                        <td><a href="index.php?page=backupjob&backupjob_name={$job.name}">{$job.name}</a></td>
                        <td>{$job.jobbytes}</td>
                        <td>{$job.jobfiles}</td>
                     </tr>
                  {foreachelse}
                     <tr> <td colspan="3" class="text-center">{t}Nothing to display{/t}</td> </tr>
                  {/foreach}
               </table>
      </div>
   </div>
  
</div> <!-- end <div class="container"> -->

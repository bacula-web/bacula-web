{include file=header.tpl}

<div class="container-fluid">
	<h3>{$page_name}</h3>
	
	<!-- First row with catalog statistics -->
	<h4>{t}Catalog statistics{/t}</h4>
	
	<div class="row">
	  <!-- Defined clients -->
	  <div class="col-xs-6 col-sm-4 col-lg-2 col-lg-offset-1">
	    <div class="panel panel-default">
	      <div class="panel-heading">{t}Clients{/t}</div>
		  <div class="panel-body text-right"><h3>{$clients}</h3></div>
	    </div> <!-- end <div class="panel panel-default"> -->
	  </div> <!-- end <div class="col-xs-2 col-xs-offset-1"> -->
	
	  <!-- Defined Jobs -->
	  <div class="col-xs-6 col-sm-4 col-lg-2">
	    <div class="panel panel-default">
		  <div class="panel-heading">{t}Jobs{/t}</div>
		  <div class="panel-body text-right"><h3>{$defined_jobs}</h3></div>
		</div> <!-- end <div class="panel ... -->
	  </div> <!-- end <div class="col- -->
	  
	  <!-- Defined FileSets -->
	  <div class="col-xs-6 col-sm-4 col-lg-2">
	    <div class="panel panel-default">
	      <div class="panel-heading">{t}Filesets{/t}</div>
		  <div class="panel-body text-right"><h3>{$defined_filesets}</h3></div>
	    </div>
	  </div>
	  
	  <!-- Stored bytes -->
	  <div class="col-xs-6 col-sm-4 col-lg-2">
	    <div class="panel panel-default">
	      <div class="panel-heading">{t}Total bytes{/t}</div>
		  <div class="panel-body text-right"><h3>{$stored_bytes}</h3></div>
		</div>
	  </div>
	  
	  <!-- Stored files -->
	  <div class="col-xs-12 col-sm-4 col-lg-2">
	    <div class="panel panel-default">
	      <div class="panel-heading">{t}Total files{/t}</div>
		  <div class="panel-body text-right"><h3>{$stored_files}</h3></div>
		</div>
	  </div>
	
	  <!-- Catalog (database) size -->
      <div class="col-xs-6 col-sm-4 col-lg-2 col-lg-offset-1">    
	    <div class="panel panel-default">
		  <div class="panel-heading">{t}Database size{/t}</div>
		  <div class="panel-body text-right"><h3>{$database_size}</h3></div>
		</div> <!-- end <div class="panel ...." -->
	  </div> <!-- end <div class="col-..." -->

      <!-- Defined pools -->
	  <div class="col-xs-6 col-sm-4 col-lg-2">
	    <div class="panel panel-default">
		  <div class="panel-heading">{t}Pool(s){/t}</div>
		  <div class="panel-body text-right"><h3>{$pools_nb}</h3></div>
		</div> <!-- end <div class="panel ...." -->
	  </div> <!-- end <div class="col-..." -->
	  
	  <!-- Defined volumes -->
	  <div class="col-xs-6 col-sm-4 col-lg-2">
	    <div class="panel panel-default">
		  <div class="panel-heading">{t}Volume(s){/t}</div>
		  <div class="panel-body text-right"><h3>{$volumes_nb}</h3></div>
		</div> <!-- end <div class="panel ...." -->
	  </div> <!-- end <div class="col-..." -->
	  
	  <!-- Volumes storage usage -->
	  <div class="col-xs-6 col-sm-4 col-lg-2">
	    <div class="panel panel-default">
		  <div class="panel-heading">{t}Volume(s) size{/t}</div>
		  <div class="panel-body text-right"><h3>{$volumes_size}</h3></div>
		</div> <!-- end <div class="panel ...." -->
	  </div> <!-- end <div class="col-..." -->

	</div> <!-- end <div class="row equalwidth"> -->
	
	<!-- Second row with Jobs statistics, stored bytes and stored files widgets -->
	<div class="row">
		<!-- Last period job status -->
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading"> <b>{t}Last period job status{/t}</b> ({$literal_period}) </div>
				<!-- Period selector -->
					<div class="panel-body">
						<form class="form-inline pull-right" method="post" role="form" action="index.php">
						    <div class="form-group form-group-sm">
                            	<label class="control-label">Period </label>
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
						<!-- Graph pre-loader -->
						<div class="img_loader text-center"> 
							<i class="fa fa-spinner fa-spin fa-2x"></i>&nbsp;
							<p>Loading graph</p> 
						</div>
						<a href="jobs.php" title="{t}Click here to see the report{/t}">
							<img src="{$graph_jobs}" class="img-responsive center-block" alt="Last period jobs">
						</a>
						<table class="table table-condensed">
							<tr>
								<td><h5>{t}Running jobs{/t}</h5></td>
								<td class="text-center"> <h4><span class="label label-default">{$running_jobs}</span></h4> </td>
							</tr>
							<tr>
 								<td><h5>{t}Completed job(s){/t}</h5></td>
								<td class="text-center"> <h4><span class="label label-success">{$completed_jobs}</span></h4> </td>
							</tr>
 							<tr>
                            	<td> <h5>{t}Waiting jobs(s){/t}</h5></td>
                                <td class="text-center"> <h4><span class="label label-primary">{$waiting_jobs}</span></h4> </td>
                            </tr>
							<tr>
                            	<td> <h5>{t}Failed job(s){/t}</h5></td>
                                <td class="text-center"> <h4><span class="label label-danger">{$failed_jobs}</span></h4> </td>
                            </tr>
							<tr>
                            	<td> <h5>{t}Canceled job(s){/t}</h5></td>
                                <td class="text-center"> <h4><span class="label label-warning">{$canceled_jobs}</span></h4> </td>
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
                    	<div class="img_loader text-center">
                    		<i class="fa fa-spinner fa-spin fa-2x"></i>&nbsp;
                        	<p>Loading graph</p>
                    	</div>						
						<img src="{$graph_stored_bytes}" class="img-responsive center-block" title="{t}Stored bytes over the last 7 days{/t}" alt="Stored Bytes over last 7 days">
					</div> <!-- end <div class="panel-body"> -->
				</div> <!-- end class="panel panel-default" -->
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading" title="{t}Stored files over the last 7 days{/t}"><b>{t}Stored Files (last 7 days){/t}</b></div>
					<div class="panel-body">
	                	<div class="img_loader text-center">
	                    	<i class="fa fa-spinner fa-spin fa-2x"></i>&nbsp;
	                        <p>Loading graph</p>
	                    </div>						
						<img src="{$graph_stored_files}" class="img-responsive center-block" title="{t}Stored files over the last 7 days{/t}" alt="Stored Files over last 7 days">
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
					<a href="pools.php" title="{t}Click here to see the report{/t}">
						<img src="{$graph_pools}" class="img-responsive center-block" alt="Pools and volumes">
					</a>
				</div>
			</div> <!-- div class="panel panel-default" -->
		</div> <!-- end class="col-xs-12 col-md-6" -->
		
		<div class="col-xs-12 col-md-6">
			<!-- Last used volumes -->
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Last used volumes{/t}</b></div>
				<div class="panel-body">
					<div class="table-responsive">
					<table class="table table-condensed table-stripped">
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

	<!-- Fourth row with Client and backup job reports widgets  -->
	<div class="row">
		<!-- Client report -->
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Client report{/t}</b></div>
				<div class="panel-body">
					<form method="post" action="client-report.php" class="form-horizontal" role="form">
						<!-- Clients list -->
						<div class="form-group">
							<label class="col-sm-2 control-label">{t}Client{/t}</label>
							<div class="col-sm-10">
								<select name="client_id" class="form-control">
									{foreach from=$clients_list key=client_id item=client_name}
									<option value="{$client_id}">{$client_name}</option>
									{/foreach}
								</select>
							</div>
						</div>
						<!-- Intervals -->
						<div class="form-group">
							<label class="col-sm-2 control-label">{t}Interval{/t}</label>
							<div class="col-sm-10">
								<select name="period" class="form-control">
									<option value="7">{t}Last week{/t}
									<option value="14">{t}Last 2 week{/t}
									<option value="28">{t}Last month{/t}
								</select>
							</div>
						</div>
						<!-- Submit button -->
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-default">{t}View report{/t}</button>
							</div>
						</div>
					</form>
				</div> <!-- end <div class="panel-body"> -->
			</div> <!-- end <div class="panel panel-default"> -->
		</div> <!-- end <div class="col-..." -->
		
		<div class="col-xs-12 col-md-6">
			<!-- Backup job report -->
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Backup Job report{/t}</b></div>
				<div class="panel-body">
					<form method="post" action="backupjob-report.php" class="form-horizontal" role="form">
						<div class="form-group">
							<label class="col-sm-4 control-label">{t}Backup job name{/t}</label>
							<div class="col-sm-8">
								<input type=hidden name="default" value="1">
								<select name=backupjob_name class="form-control">{html_options values=$jobs_list output=$jobs_list}</select>
							</div>
						</div>
						<!-- Submit button -->
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-default">{t}View report{/t}</button>
							</div>
						</div>
					</form>
				</div>
			</div>		
		</div> <!-- end <div class="col-..." -->
	</div> <!-- end <div class="row"> -->
</div> <!-- end <div class="container"> -->

{include file="footer.tpl"}

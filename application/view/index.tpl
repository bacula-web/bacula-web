{include file=header.tpl}

<div class="container-fluid">
  
  <div class="row">
    <!-- Left column -->
    <div class="col-md-3 col-lg-3">
      
      <div class="panel panel-default">
        
        <div class="panel-heading">
          <b>
            {t}Catalog statistics{/t}
          </b>
        </div>
        <div class="panel-body">
          
          <table class="table table-condensed table-striped">
            <tr>
              <td>
                {t}Clients{/t}
              </td>
              <td class="text-right">
                
                <h3>
                  {$clients}
                </h3>
              </td>
            </tr>
            <tr>
              <td title="Defined Jobs">
                {t}Jobs{/t}
              </td>
              <td class="text-right">
                
                <h3>
                  {$defined_jobs}
                </h3>
              </td>
            </tr>
            <tr>
              <td title="Defined Filesets">
                {t}Filesets{/t}
              </td>
              <td class="text-right">
                
                <h3>
                  {$defined_filesets}
                </h3>
              </td>
            </tr>
            <tr>
              <td>
                {t}Total bytes{/t}
              </td>
              <td class="text-right">
                
                <h3>
                  {$stored_bytes}
                </h3>
              </td>
            </tr>
            <tr>
              <td>
                {t}Total files{/t}
              </td>
              <td class="text-right">
                
                <h3>
                  {$stored_files}
                </h3>
              </td>
            </tr>
            <tr>
              <td>
                {t}Database size{/t}
              </td>
              <td class="text-right">
                
                <h3>
                  {$database_size}
                </h3>
              </td>
            </tr>
            <tr>
              <td>
                {t}Pool(s){/t}
              </td>
              <td class="text-right">
                
                <h3>
                  {$pools_nb}
                </h3>
              </td>
            </tr>
            <tr>
              <td>
                {t}Volume(s){/t}
              </td>
              <td class="text-right">
                
                <h3>
                  {$volumes_nb}
                </h3>
              </td>
            </tr>
            <tr>
              <td>
                {t}Volume(s) size{/t}
              </td>
              <td class="text-right">
                
                <h3>
                  {$volumes_size}
                </h3>
              </td>
            </tr>
          </table>
        </div>
        
        <!-- div class="panel-body" -->
      </div>
      
      <!-- div class="panel panel-default" -->
    </div>
    
    <!-- div class="col-.. -->
    
	<!-- Right column -->
    <div class="col-md-9 col-lg-9">
      
      <div class="row">
        
        <div class="col-md-6 col-lg-6">
          
          <div class="well well-sm">
            <b>{t}Overall status{/t}</b> ({$literal_period})
		  </div>

        </div>
	    <div class="col-md-4 col-lg-4">
          <form class="form-inline pull-right" method="post" role="form" action="index.php">
            <label>Period</label>
            
            <select class="form-control" name="period_selector">
              {foreach
              from=$custom_period_list key=period_id item=period_label} 
              <option value="{$period_id}"
              {if $period_id eq $custom_period_list_selected} selected {/if}>
                {$period_label} 
              </option>
              {/foreach} 
            </select>
            <button title="{t}Update with selected period{/t}" class="btn btn-default" type="submit">
              {t}Submit{/t}
            </button>
            
          </form>
		  
        </div>
      </div>
      <div class="row">
		<div class="col-md-6 col-lg-5">
          
          <div class="panel panel-default">
            
            <div class="panel-heading">
              <b>
                {t}Last period job status{/t}
              </b>
            </div>
            <div class="panel-body">
              
              <table class="table table-condensed table-striped">
                <tr>
                  <td>
                    {t}Running jobs{/t}
                  </td>
                  <td>
                    {$running_jobs}
                  </td>
                </tr>
                <tr>
                  <td>
                    {t}Completed job(s){/t}
                  </td>
                  <td>
                    {$completed_jobs}
                  </td>
                </tr>
                <tr>
                  <td>
                    {t}Waiting job(s){/t}
                  </td>
                  <td>
                    {$waiting_jobs}
                  </td>
                </tr>
                <tr>
                  <td>
                    {t}Failed job(s){/t}
                  </td>
                  <td>
                    {$failed_jobs}
                  </td>
                </tr>
                <tr>
                  <td>
                    {t}Canceled job(s){/t}
                  </td>
                  <td>
                    {$canceled_jobs}
                  </td>
                </tr>
                <tr>
                  <td>
                    Job Level (Incr / Diff / Full)
                  </td>
                  <td>
                    {$incr_jobs} / {$diff_jobs} / {$full_jobs}
                  </td>
                </tr>
                <tr>
                  <td>
                    {t}Transferred Bytes / Files{/t}
                  </td>
                  <td>
                    {$bytes_last} / {$files_last}
                  </td>
                </tr>
              </table>
            </div>
            
            <!-- Div class="panel-body" -->
          </div>
          
          <!-- div class="panel panel-default" -->
        </div>
        
        <!-- div class="col-md ..." -->
        
	    <!-- Last period jobs status graph -->
		<div class="col-md-6 col-lg-5">
          <div class="panel panel-default">
            
            <div class="panel-heading">
              <b>
                {t}Jobs status{/t}
              </b>
            </div>
            <div class="panel-body">
              
              <a href="jobs.php" title="{t}Click here to see the report{/t}">
                <img alt="" src="{$graph_jobs}" class="img-responsive" />
                
              </a>
              
            </div>
          </div>
          
          <!-- div class="panel panel-default" -->
        </div>
        
        <!-- div class="col-md-5 ..." -->
        
      </div>
      
      <!-- div class="row" -->
	  <div class="row">
		<!-- Pools and volumes status -->
	    <div class="col-md-6 col-lg-5">
          
		  <div class="panel panel-default">
            
			<div class="panel-heading">
              <b>
                {t}Pools and volumes status{/t}
              </b>
            </div>
			<div class="panel-body">
			  <a href="pools.php" title="{t}Click here to see the report{/t}">
                
				<img alt="" src="{$graph_pools}" class="img-responsive" />
                
			  </a>
              
			</div>
		  </div>
          
          <!-- div class="panel panel-default" -->
	    </div>
        
        <!-- div class="col-..." -->
        
		<!-- Stored Bytes for last 7 days -->
		<div class="col-md-6 col-lg-5">
	      <div class="panel panel-default">
		    <div class="panel-heading">
              <b>
                {t}Stored Bytes{/t}
              </b>
            </div>
			<div class="panel-body">
			  <img alt="" src="{$graph_stored_bytes}" class="img-responsive"/>
			</div>
		  </div>
		</div>
      </div>
      
      <!-- div class="row" -->
	  
	  <div class="row">
	    <div class="col-md-6 col-lg-6">
          <!-- Last used volumes -->
          
          <div class="panel panel-default">
            <div class="panel-heading">
              <b>
                {t}Last used volumes{/t}
              </b>
            </div>
            <div class="panel-body">
              <table class="table table-condensed table-stripped">
                <tr>
                  <th title="Volume name">
                    Volume
                  </th>
                  
                  <th title="Volume status">
                    Status
                  </th>
                  
                  <th title="Volume pool">
                    Pool
                  </th>
                  
                  <th title="Last written date for this volume">
                    Last written
                  </th>
                  
                  <th title="Number of jobs">
                    Jobs
                  </th>
                  
                </tr>
                {foreach from=$volumes_list item=vol} 
                <tr class="{$vol.odd_even}">
                  <td>
                    {$vol.volumename}
                  </td>
                  <td>
                    {$vol.volstatus}
                  </td>
                  <td>
                    {$vol.poolname}
                  </td>
                  <td>
                    {$vol.lastwritten}
                  </td>
                  <td class="strong">
                    {$vol.voljobs}
                  </td>
                </tr>
                {/foreach} 
              </table>
            </div>
          
        </div> 
      </div> <!-- div class="col-md..." -->
      
      </div> <!-- row -->
	  
	  <div class="row">
	    <!-- Client report -->
	    <div class="col-md-6 col-lg-6">
        
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
			</div>
		</div>
		</div> <!-- div class="col-md-6 ..." -->
		
		<div class="col-md-6 col-lg-6">
		<!-- Backup job report -->
		  <div class="panel panel-default">
		    <div class="panel-heading"><b>{t}Backup Job report{/t}</b></div>
			<div class="panel-body">
				<form method="post" action="backupjob-report.php" class="form-horizontal" role="form">
				  <div class="form-group">
				    <label class="col-sm-4 control-label">{t}Backup job name{/t}</label>
				    <div class="col-sm-8">
				      <input type=hidden name="default" value="1">
				      <select name=backupjob_name class="form-control"> 
					    {html_options values=$jobs_list output=$jobs_list} 
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
			</div>
		  </div>

		</div>
	  </div>
      
	</div> <!-- div class="col-md-9 ... -->
    
  </div> <!-- Main div class="row" -->
  
  </div><!-- div class="container-fluid"-->
  {include file="footer.tpl"} 
<div class="container" id="jobsreport">

	<div class="page-header">
		<h3>{$page_name} <small>{t}Bacula History Files{/t}</small></h3>
	</div>

	<div class="row">
  <div class="col-md-7">
   
		 <!-- Backup job report -->
		 <div class="panel panel-default">
			<div class="panel-heading"><b>{t}Job Info{/t}</b></div>
			<div class="panel-body">
				<div>
					<b>{t}Job Name{/t}</b>: <a href="index.php?page=backupjob&backupjob_name={$job_info.name}">{$job_info.name}</a>
					<br>
					<b>{t}Job Status{/t}</b>: {$job_info.jobstatus}
				</div>
		    </div> <!-- end div class=panel-body -->
		  </div> <!-- end div class=panel ... -->
      </div> <!-- div class="col... -->

      <!-- Search box -->
      <div class="col-md-5">
        <div class="panel panel-default">
          <div class="panel-heading"><b>Search</b></div>
          <div class="panel-body"> 
            <form class="form-inline" action="index.php?page=jobfiles&jobId={$jobid}" method="post">
              <div class="form-group">
                <label for="InputFilename">Filename</label>
                <input type="text" class="form-control" name="InputFilename" id="InputFilename" placeholder="{$filename}">
                <input type="hidden" name="jobId" value="{$jobid}">
                <button type="submit" class="btn btn-default">Search</button>
                <button type="reset" class="btn btn-default" title="{t}Reset{/t}">{t}Reset{/t}</button>
              </div> <!-- end div class="form-group -->
            </form>
          </div> <!-- end div class="panel-body -->
        </div> <!-- end div class="panel -->
      </div> <!-- end div class="col..." -->
	
    <div class="col-md-12">	
		<div class="table-responsive">
			<table class="table table-condensed table-hover table-striped table-bordered text-center">
				<tr>
					<th class="text-center">{t}File Index{/t}</th>
					<th class="text-center">{t}Filename{/t}</th>
                
				</tr>
				{foreach from=$job_files item=file}
				<tr>
					<td>#{$file.fileindex}</td>
					<td class="text-left">{$file.path}{$file.filename}</td>
				</tr>
				{foreachelse}
				<tr>
					<td colspan="4">{t}No file(s) to display{/t}</td>
				</tr>
				{/foreach}
			</table>
		</div>
			
		<div class="panel panel-info">
		  <div class="panel-heading">	
			 {if $pagination_active}
				<nav aria-label="...">
					<ul class="pager">
						<li>
                     {* if we are on first page *}
							{if $pagination_current_page == 0}
								<a class="disabled">Previous</a>
							{else}
								<a href="index.php?page=jobfiles&jobId={$jobid}&paginationCurrentPage={$pagination_current_page-1}
                        {if $filename != ''}
                          &InputFilename={$filename}
                        {/if}
                        ">Previous</a>
							{/if}
						</li>
						<li>Found <b>{$job_files_count}</b> File(s)</b></li>
						<li>
                     {* if there is only one page *}
							{if $job_files_count_paging == $pagination_rows_per_page}
								<a href="index.php?page=jobfiles&jobId={$jobid}&paginationCurrentPage={$pagination_current_page+1}
                        {if $filename != ''}
                          &InputFilename={$filename}
                        {/if}
                        ">Next</a>
							{else}
								<a class="disabled">Next</a>
							{/if}							
						</li>
					</ul>
				</nav>
           </div> <!-- end div class="panel-heading ... -->
			</div> <!-- end div class="panel-->
			{/if}
		</div>
     </div> <!-- end div class="col... -->
	</div> <!-- end div class="row... -->
</div>

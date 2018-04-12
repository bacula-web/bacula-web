<div class="container-fluid" id="jobsreport">

	<div class="page-header">
		<h3>{$page_name} <small>{t}Bacula History Files{/t}</small></h3>
	</div>

	<div class="row">
		
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
		
		<div class="table-responsive">
			<table class="table table-condensed table-hover table-striped table-bordered text-center">
				<tr>
					<th class="text-center">{t}File Index{/t}</th>
					<th class="text-center">{t}File Path{/t}</th>
				</tr>
				{foreach from=$history_files item=file}
				<tr>
					<td>#{$file.fileindex}</td>
					<td class="text-left">{$file.path}</td>
				</tr>
				{foreachelse}
				<tr>
					<td colspan="4">{t}No file(s) to display{/t}</td>
				</tr>
				{/foreach}
			</table>
		</div>
			
		<div class="alert alert-info text-center" role="alert">
			
			{if $pagination_active}
			<div class="bs-example" data-example-id="simple-pager">
				<nav aria-label="...">
					<ul class="pager">
						<li>
							{if $pagination_current_page == 0}
								<a class="disabled">Previous</a>
							{else}
								<a href="index.php?page=historyfiles&jobId={$jobid}&paginationCurrentPage={$pagination_current_page-1}">Previous</a>
							{/if}
						</li>
						<li>Found <b>{$history_files_count}</b> File(s)</b></li>
						<li>
							{if $history_files_count_paging == $pagination_rows_per_page}
								<a href="index.php?page=historyfiles&jobId={$jobid}&paginationCurrentPage={$pagination_current_page+1}">Next</a>
							{else}
								<a class="disabled">Next</a>
							{/if}							
						</li>
					</ul>
				</nav>
			</div>
			{/if}
		</div>
	</div>
  
</div>
{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Job files{/t}</title>
{/block}

{block name=body}
    <div class="container-fluid" id="jobsreport">

        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-3 mb-0">{t}Job files{/t}</h3>
                <p class="mb-0">{t}Bacula History Files{/t}</p>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">

                <!-- Backup job report -->
                <div class="card">
                    <div class="card-header">{t}Job Info{/t}</div>
                    <div class="card-body">
                        <div>
                            <p><b>{t}Job Name{/t}</b>: {$job_info.name}</p>
                            <p><b>{t}Job Status{/t}</b>: {$job_info.jobstatus}</p>
                            <form action="/backupjob" method="post">
                                <input type="hidden" name="backupjob_name" value="{$job_info.name}" />
                                <input type="hidden" name="backupjob_period" value="7" />
                                <button type="submit" class="btn btn-sm btn-primary">{t}View backup job{/t}</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Search box -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header"><b>Search</b></div>
                    <div class="card-body">
                        <form action="/jobfiles/{$jobid}" method="post">
                            <div class="mb-3">
                                <label for="InputFilename" class="form-label">Filename</label>
                                <input type="text" class="form-control" name="InputFilename" id="InputFilename"
                                       placeholder="search any file or folder name" value="{$filename}">
                            </div>
                                <input type="hidden" name="jobId" value="{$jobid}">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <button type="reset" class="btn btn-default" title="{t}Reset{/t}">{t}Reset{/t}</button>
                            </div> <!-- end div class="form-group -->
                        </form>
                    </div>
                </div>
            </div>

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
                                <td colspan="2">{t}No file(s) to display{/t}</td>
                            </tr>
                        {/foreach}
                    </table>
                </div>

                <div class="col-12">
                    {if $pagination_active}
                        <nav aria-label="pagination">
                            <ul class="pagination justify-content-center">
                                {* if we are on first page *}
                                {if $pagination_current_page == 0}
                                <li class="page-item disabled">
                                    <a class="page-link">Previous</a>
                                    {else}
                                <li class="page-item">
                                    <a class="page-link"
                                       href="/jobfiles/{$jobid}&paginationCurrentPage={$pagination_current_page-1}
                        {if $filename != ''}
                          &InputFilename={$filename}
                        {/if}
                        ">Previous</a>
                                    {/if}
                                </li>
                                <li class="page-item disabled"><a class="page-link">Found <b>{$job_files_count}</b>
                                        File(s)</b></a></li>
                                <li class="page-item">
                                    {* if there is only one page *}
                                    {if $job_files_count_paging == $pagination_rows_per_page}
                                        <a class="page-link"
                                           href="/jobfiles/{$jobid}&paginationCurrentPage={$pagination_current_page+1}
                                        {if $filename != ''}
                                            &InputFilename={$filename}
                                        {/if}
                                        ">Next</a>
                                    {else}
                                        <a class="page-link">Next</a>
                                    {/if}
                                </li>
                            </ul>
                        </nav>
                    {/if}
                </div>
            </div>
        </div> <!-- end div class="col... -->
    </div>
    <!-- end div class="row... -->
    </div>
{/block}
{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Backup job report{/t}</title>
{/block}

{block name=body}
    <div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <h3 class="mt-3 mb-0">{t}Backup job report{/t}</h3>
            <p class="mb-0">{t}Report per Bacula backup job name{/t}</p>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="card border-dark mb-3">
            <div class="card-header">{t}Report options{/t}</div>
            <div class="card-body">
                <form method="post" action="/backupjob" class="form-inline">
                    <!-- Backup job name -->
                    <div class="form-group">
                        <label for="backupjobname">{t}Backup job name{/t}</label>
                        <input type=hidden id="backupjobname" name="default" value="1"> <!-- <- what is it ? -->
                        {html_options class="form-control" name=backupjob_name output=$jobs_list values=$jobs_list selected=$selected_jobname}
                    </div>
                    <!-- Period -->
                    <div class="form-group">
                        <label for="period">{t}Period{/t}</label>
                        {html_options class="form-control" name=period options=$periods_list selected=$selected_period}
                    </div>

                    <!-- Submit button -->
                    <div class="form-group pull-right">
                        <button type="submit" class="btn btn-primary">{t}View report{/t}</button>
                    </div>
                </form>
            </div>
        </div>

        {if $no_report_options == 'false'}
        <div class="card border-dark mb-3">
            <div class="card-header">{t}Job informations{/t}</div>
            <div class="card-body">
                <h6 class="card-title">{t}Job name{/t}</h6>
                <p class="card-text">{$backupjob_name}</p>
                <h6 class="card-title">{t}Period{/t}</h6>
                <p class="card-text">{$perioddesc}</p>
                <h6 class="card-title">{t}Transfered Bytes{/t}</h6>
                <p class="card-text">{$backupjobbytes}</p>
                <h6 class="card-title">{t}Transfered Files{/t}</h6>
                <p class="card-text">{$backupjobfiles}</p>
            </div>
        </div>

        <!-- Last jobs list -->
        <h4>{t}Last backup jobs{/t}</h4>

        <div class="table-responsive">
            <table class="table table-condensed table-hover table-striped table-bordered text-center">
                <tr>
                    <th class="text-center">{t}Job Id{/t}</th>
                    <th class="text-center">{t}Status{/t}</th>
                    <th class="text-center">{t}Level{/t}</th>
                    <th class="text-right">{t}Files{/t}</th>
                    <th class="text-right">{t}Bytes{/t}</th>
                    <th class="text-center">{t}Start time{/t}</th>
                    <th class="text-center">{t}End time{/t}</th>
                    <th class="text-center">{t}Elapsed time{/t}</th>
                    <th class="text-rightr">{t}Speed{/t}</th>
                    <th class="text-center">{t}Compression{/t}</th>
                </tr>
                {foreach from=$jobs item=job}
                    <tr>
                        <td>{$job.jobid}</td>
                        <td>{$job.jobstatuslong}</td>
                        <td>{$job.joblevel}</td>
                        <td class="text-right">
                            {if ($job.jobfiles > 0) }
                                <a href="index.php?page=jobfiles&jobId={$job.jobid}">{$job.jobfiles}</a>
                            {else}
                                {$job.jobfiles}
                            {/if}
                        </td>
                        <td class="text-right">{$job.jobbytes}</td>
                        <td>{$job.starttime}</td>
                        <td>{$job.endtime}</td>
                        <td>{$job.elapsedtime}</td>
                        <td class="text-right">{$job.speed}</td>
                        <td>{$job.compression}</td>
                    </tr>
                    {foreachelse}
                    <tr>
                        <td colspan="9">{t}No job(s) to display{/t}</td>
                    </tr>
                {/foreach}
            </table>
        </div>
    </div>

    <!-- Transferred Bytes/Files graph -->
    {if ($selected_period <= 14) }
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="card">
                <div class="card-header"><b>{t}Transfered Bytes{/t}</b></div>
                <div class="card-body">
                    <div id="{$stored_bytes_chart_id}">
                        <svg></svg>
                    </div>
                    {$stored_bytes_chart}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <div class="card">
                <div class="card-header"><b>{t}Transfered Files{/t}</b></div>
                <div class="card-body">
                    <div id="{$stored_files_chart_id}">
                        <svg></svg>
                    </div>
                    {$stored_files_chart}
                </div>
            </div>
        </div>
        {else}
        <div class="row">
            <div class="col-xs-12">
                <div class="card">
                    <div class="card-header"><b>{t}Transfered Bytes{/t}</b></div>
                    <div class="card-body">
                        <div id="{$stored_bytes_chart_id}">
                            <svg></svg>
                        </div>
                        {$stored_bytes_chart}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="card">
                    <div class="card-header"><b>{t}Transfered Files{/t}</b></div>
                    <div class="card-body">
                        <div id="{$stored_files_chart_id}">
                            <svg></svg>
                        </div>
                        {$stored_files_chart}
                    </div>
                </div>
            </div>
            {/if}
            {else}
            <div class="alert alert-info"
                 role="alert">{t}Choose the backup job name and the period interval, then click on the{/t}
                <strong>{t}View report{/t}</strong> {t}button{/t}</div>
            {/if}
        </div>
    </div>
{/block}
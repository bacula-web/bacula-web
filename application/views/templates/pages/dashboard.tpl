{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Dashboard{/t}</title>
{/block}

{block name=body}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-3 mb-0">{t}Dashboard{/t}</h3>
                <p class="mb-0">{t}General overview{/t}</p>
                <hr>
            </div>
        </div>

        <!-- First row with Jobs statistics, stored bytes and stored files widgets -->
        <div class="row">
            <!-- Last period job status -->
            <div class="col-6">

                <div class="card border-dark mb-3">
                    <div class="card-header"><b>{t}Last period job status{/t}</b> ({$literal_period})</div>
                    <!-- Period selector -->
                    <div class="card-body">
                        <form class="form-inline pull-right" method="post" role="form" action="/">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">{t}Period{/t} </span>
                                <select class="form-control input-sm" name="period_selector">
                                    {foreach from=$custom_period_list key=period_id item=period_label}
                                        <option value="{$period_id}"
                                                {if $period_id eq $custom_period_list_selected} selected {/if}>{$period_label}
                                        </option>
                                    {/foreach}
                                </select>
                                <button title="{t}Update with selected period{/t}" class="btn btn-primary btn-sm"
                                        type="submit">{t}Submit{/t}</button>
                            </div>
                        </form>
                    </div>

                    <!-- Last period job status graph -->
                    <div class="card-body">
                        <div id="{$last_jobs_chart_id}">
                            <svg></svg>
                        </div>
                        {$last_jobs_chart}

                        <table class="table table-condensed">
                            <tr>
                                <td><h5>{t}Running jobs{/t}</h5></td>
                                <td class="text-center">
                                    <form action="/jobs" method="post">
                                        <input type="hidden" name="filter_jobstatus" value="1" />
                                        <button class="btn btn-lg btn-link type="submit">{$running_jobs}</button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><h5>{t}Completed job(s){/t}</h5></td>
                                <td class="text-center">
                                    <form action="/jobs" method="post">
                                        <input type="hidden" name="filter_jobstatus" value="3" />
                                        <button class="btn btn-lg btn-link type="submit">{$completed_jobs}</button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><h5>{t}Completed with errors job(s){/t}</h5></td>
                                <td class="text-center">
                                    <form action="/jobs" method="post">
                                        <input type="hidden" name="filter_jobstatus" value="4" />
                                        <button class="btn btn-lg btn-link type="submit">{$completed_with_errors_jobs}</button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><h5>{t}Waiting jobs(s){/t}</h5></td>
                                <td class="text-center">
                                    <form action="/jobs" method="post">
                                        <input type="hidden" name="filter_jobstatus" value="2" />
                                        <button class="btn btn-lg btn-link type="submit">{$waiting_jobs}</button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><h5>{t}Failed job(s){/t}</h5></td>
                                <td class="text-center">
                                    <form action="/jobs" method="post">
                                        <input type="hidden" name="filter_jobstatus" value="5" />
                                        <button class="btn btn-lg btn-link type="submit">{$failed_jobs}</button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><h5>{t}Canceled job(s){/t}</h5></td>
                                <td class="text-center">
                                    <form action="/jobs" method="post">
                                        <input type="hidden" name="filter_jobstatus" value="6" />
                                        <button class="btn btn-lg btn-link type="submit">{$canceled_jobs}</button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><h5>{t}Job Level (Incr / Diff / Full){/t}</h5></td>
                                <td class="text-center"><h4>{$incr_jobs} / {$diff_jobs} / {$full_jobs} </h4></td>
                            </tr>
                            <tr>
                                <td><h5>{t}Transferred Bytes / Files{/t}</h5></td>
                                <td class="text-center"><h4>{$bytes_last} / {$files_last} </h4></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="row">
                    <!-- Stored Bytes for last 7 days -->
                    <div class="col-12">
                        <div class="card border-dark mb-3">
                            <div class="card-header" title="{t}Stored bytes over the last 7 days{/t}">
                                <b>{t}Stored Bytes (last 7 days){/t}</b></div>
                            <div class="card-body">
                                <div id="{$storedbytes_chart_id}">
                                    <svg></svg>
                                </div>
                                {$storedbytes_chart}
                            </div>
                        </div>
                    </div>

                    <!-- Stored Files for last 7 days -->
                    <div class="col-12">
                        <div class="card border-dark mb-3">
                            <div class="card-header" title="{t}Stored files over the last 7 days{/t}">
                                <b>{t}Stored Files (last 7 days){/t}</b
                            </div>
                            <div class="card-body">
                                <div id="{$storedfiles_chart_id}">
                                    <svg></svg>
                                </div>
                                {$storedfiles_chart}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Third row with Pools and volumes status + Last used volumes widgets -->
        <div class="row">
            <!-- Pools and volumes status -->
            <div class="col-xs-12 col-md-6">
                <div class="card border-dark mb-3">
                    <div class="card-header">
                        <b>{t}Pools and volumes status{/t}</b>
                    </div>
                    <div class="card-body">
                        <div id="{$pools_usage_chart_id}">
                            <svg></svg>
                        </div>
                        {$pools_usage_chart}
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-6">
                <!-- Last used volumes -->
                <div class="card border-dark mb-3">
                    <div class="card-header">
                        <b>{t}Last used volumes{/t}</b>
                        <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="custom-tooltip"
                                data-bs-title="{t}Displays the last 10 volumes used during backups{/t}">
                            <i class="fa-solid fa-info"></i>
                        </button>
                    </div>

                    <div class="card-body">
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
                                        <td>
                                            <a href="/volumes/{$vol.mediaid}"
                                               title="{t}Show volume{/t}">{$vol.volumename}</a>
                                        </td>
                                        <td>{$vol.volstatus}</td>
                                        <td>{$vol.poolname}</td>
                                        <td>{$vol.lastwritten}</td>
                                        <td class="strong">{$vol.voljobs}</td>
                                    </tr>
                                {/foreach}
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Clients job total -->
        <div class="row">
            <div class="col-xs-12 col-md-6">

                <!-- Clients jobs total widget -->
                <div class="card border-dark mb-3">
                    <div class="card-header">
                        <b>{t}Clients jobs total{/t}</b>
                    </div>
                    <div class="card-body">
                        <p>{t}Per job name backup and restore jobs statistics{/t}</p>
                    </div>
                    <table class="table table-condensed">
                        <tr>
                            <th>{t}Job name{/t}</th>
                            <th class="text-right">{t}Jobs{/t}</th>
                            <th class="text-right">{t}Files{/t}</th>
                            <th class="text-right">{t}Bytes{/t}</th>
                            <th>{t}Type{/t}</th>
                        </tr>
                        {foreach from=$jobnames_jobs_stats item=jobname}
                            <tr>
                                <td>{$jobname.jobname}</td>
                                <td class="text-right">{$jobname.jobscount}</td>
                                <td class="text-right">{$jobname.jobfiles}</td>
                                <td class="text-right">{$jobname.jobbytes}</td>
                                <td>{$jobname.type}</td>
                            </tr>
                        {/foreach}
                    </table>
                    <div class="card-body">
                        <p>Per job type backup and restore jobs statistics</p>
                    </div>
                    <table class="table table-condensed">
                        <tr>
                            <th>{t}Type{/t}</th>
                            <th class="text-right">{t}Files{/t}</th>
                            <th class="text-right">{t}Bytes{/t}</th>
                            <th class="text-right">{t}Jobs{/t}</th>
                        </tr>
                        {foreach from=$jobtypes_jobs_stats item=jobtype}
                            <tr>
                                <td>{$jobtype.type}</td>
                                <td class="text-right">{$jobtype.jobfiles}</td>
                                <td class="text-right">{$jobtype.jobbytes}</td>
                                <td class="text-right">{$jobtype.jobscount}</td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>

            <!-- Weekly jobs statistics -->
            <div class="col col-xs-12 col-md-6">
                <div class="card border-dark mb-3">
                    <div class="card-header">
                        <b>{t}Weekly jobs statistics{/t}</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-condensed table-striped">
                            <tr>
                                <th>{t}Day of week{/t}</th>
                                <th class="text-right">{t}Bytes{/t}</th>
                                <th class="text-right">{t}Files{/t}</th>
                            </tr>
                            {foreach from=$weeklyjobsstats item=day}
                                <tr>
                                    <td>{$day.dayofweek}</td>
                                    <td class="text-right">{$day.jobbytes}</td>
                                    <td class="text-right">{$day.jobfiles}</td>
                                </tr>
                                {foreachelse}
                                <tr>
                                    <td colspan="3" class="text-center">{t}Nothing to display{/t}</td>
                                </tr>
                            {/foreach}
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biggest backup jobs -->
        <div class="row">
            <div class="col col-xs-12 col-md-6">
                <!-- 10th biggest job names -->
                <div class="card border-dark mb-3">
                    <div class="card-header">
                        <b>{t}Biggest backup jobs{/t}</b>
                        <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="custom-tooltip"
                                data-bs-title="{t}Displays the 10 biggest (Bytes) Bacula backup jobs{/t}">
                            <i class="fa-solid fa-info"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-condensed table-striped">
                            <tr>
                                <th>{t}Job name{/t}</th>
                                <th class="text-right">{t}Bytes{/t}</th>
                                <th class="text-right">{t}Files{/t}</th>
                            </tr>
                            {foreach from=$biggestjobs item=job}
                                <tr>
                                    <td>
                                        <form action="/backupjob" method="post">
                                            <input type="hidden" name="backupjob_name" value="{$job.name}" />
                                            <button class="btn btn-link type="submit">{$job.name}</button>
                                        </form>
                                    </td>
                                    <td class="text-right">{$job.jobbytes}</td>
                                    <td class="text-right">{$job.jobfiles}</td>
                                </tr>
                            {foreachelse}
                                <tr>
                                    <td colspan="3" class="text-center">{t}Nothing to display{/t}</td>
                                </tr>
                            {/foreach}
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
{/block}
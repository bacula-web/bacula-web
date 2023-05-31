{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Jobs report{/t}</title>
{/block}

{block name=body}
    <div class="container-fluid" id="jobsreport">

        <div class="row">
            <div class="col-12">
                <h3 class="mt-3 mb-0">{t}Jobs report{/t}</h3>
                <p class="mb-0">{t}Bacula jobs overview{/t}</p>
                <hr>
            </div>
        </div>

        <div class="row">
            <!-- Filter jobs form -->
            <div class="col-xs-12 col-sm-3 col-sm-push-9 col-lg-2 col-lg-push-10">

                <form class="form" role="form" action="/jobs" method="post">

                    <span class="help-block">{t}Filter{/t}</span>

                    <div class="form-group">
                        <label>{t}Job status{/t}</label>
                        {html_options class="form-control" name=filter_jobstatus options=$job_status selected=$filter_jobstatus}
                    </div>

                    <div class="form-group">
                        <label>{t}Level{/t}</label>
                        {html_options class="form-control" name=filter_joblevel options=$levels_list selected=$filter_joblevel}
                    </div>

                    <div class="form-group">
                        <label>{t}Type{/t}</label>
                        {html_options class="form-control" name=filter_jobtype options=$job_types_list selected=$filter_jobtype}
                    </div>

                    <div class="form-group">
                        <label>{t}Client{/t}</label>
                        {html_options class="form-control" name=filter_clientid options=$clients_list selected=$filter_clientid}
                    </div>

                    <div class="form-group">
                        <label>{t}Pool{/t}</label>
                        {html_options class="form-control" name=filter_poolid options=$pools_list selected=$filter_poolid}
                    </div>

                    <div class="form-group">
                        <label for="datetimepicker1Input" class="form-label">{t}Start time{/t}</label>
                        <div class="input-group log-event" id="datetimepicker1" data-td-target-input="nearest" data-td-target-toggle="nearest">
                            <input id="datetimepicker1Input" name="filter_job_starttime" type="text" class="form-control" data-td-target="#datetimepicker1" value="{$filter_job_starttime}" />
                            <span class="input-group-text" data-td-target="#datetimepicker1" data-td-toggle="datetimepicker"> <i class="fas fa-calendar"></i> </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="datetimepicker1Input" class="form-label">{t}End time{/t}</label>
                        <div class="input-group log-event" id="datetimepicker2" data-td-target-input="nearest" data-td-target-toggle="nearest">
                            <input id="datetimepicker1Input" name="filter_job_endtime" type="text" class="form-control" data-td-target="#datetimepicker2" value="{$filter_job_endtime}" />
                            <span class="input-group-text" data-td-target="#datetimepicker2" data-td-toggle="datetimepicker"> <i class="fas fa-calendar"></i> </span>
                        </div>
                    </div>

                    <span class="help-block">{t}Options{/t}</span>

                    <label>{t}Order by{/t}</label>
                    {html_options class="form-control" name=filter_job_orderby options=$result_order selected=$result_order_field}

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="filter_job_orderby_asc"
                                   value="{t}ASC{/t}" {$result_order_asc_checked}> Up
                        </label>
                    </div>

                    <button type="reset" class="btn btn-default btn-sm" title="{t}Reset{/t}">{t}Reset{/t}</button>
                    <button type="submit" class="btn btn-primary btn-sm pull-right"
                            title="{t}Apply filter and options{/t}">{t}Apply{/t}</button>

                    <a class="btn btn-link btn-sm" title="{t}Reset to default{/t}" href="/jobs"
                       role="button">{t}Reset to default{/t}</a>
                </form>

            </div> <!-- div class="col-md-3 cold-lg-3" -->

            <div class="col-xs-12 col-sm-9 col-sm-pull-3 col-lg-10 col-lg-pull-2">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped text-center">
                        <thead>
                        <tr>
                            <th class="text-center">{t}Status{/t}</th>
                            <th class="text-center">{t}Job ID{/t}</th>
                            <th class="text-left">{t}Name{/t}</th>
                            <th class="text-center">{t}Type{/t}</th>
                            <th class="text-center">{t}Scheduled Time{/t}</th>
                            <th class="text-center">{t}Start time{/t}</th>
                            <th class="text-center">{t}End time{/t}</th>
                            <th class="text-center">{t}Elapsed time{/t}</th>
                            <th class="text-center">{t}Level{/t}</th>
                            <th class="text-center">{t}Bytes{/t}</th>
                            <th class="text-center">{t}Files{/t}</th>
                            <th class="text-center">{t}Speed{/t}</th>
                            <th class="text-center">{t}Compression{/t}</th>
                            <th class="text-center">{t}Pool{/t}</th>
                            <th class="text-center">{t}Log{/t}</th>
                        </tr>
                        </thead>

                        <!-- <div class="listbox"> -->
                        {foreach from=$last_jobs item=job}
                            <tr>
                                <td>
                                    <i title="{$job.jobstatuslong}" class="{$job.Job_icon}"></i>
                                </td>
                                <td>{$job.jobid}</td>
                                <td class="text-left">
                                    {if $job.type == 'B'}
                                        <form action="/backupjob" method="post">
                                            <input type="hidden" name="backupjob_name" value="{$job.job_name}" />
                                            <input type="hidden" name="backupjob_period" value="7" />
                                            <button type="submit" class="btn btn-sm btn-link">{$job.job_name}</button>
                                        </form>
                                    {else}
                                        {$job.job_name}
                                    {/if}
                                </td>
                                <td>{$job.type}</td>
                                <td>{$job.schedtime}</td>
                                <td>{$job.starttime}</td>
                                <td>{$job.endtime}</td>
                                <td>{$job.elapsed_time}</td>
                                <td>{$job.level}</td>
                                <td class="text-right">{$job.jobbytes}</td>
                                <td class="text-right">
                                    {if $job.jobfiles != 0 && $job.type == 'B'}
                                        <a href="/jobfiles/{$job.jobid}"
                                           title="{t}Show job files{/t}">
                                            {$job.jobfiles} <i class="fa-solid fa-folder"></i>
                                        </a>
                                    {else}
                                        {$job.jobfiles}
                                    {/if}
                                </td>
                                <td>{$job.speed}</td>
                                <td>{$job.compression}</td>
                                <td>{$job.pool_name}</td>
                                <td>
                                    <a href="/joblog/{$job.jobid}" title="{t}Show job logs{/t}"> <i
                                                class="fa-solid fa-magnifying-glass"></i> </a>
                                </td>
                            </tr>
                            {foreachelse}
                            <tr>
                                <td colspan="12">{t}No job(s) to display{/t}</td>
                            </tr>
                        {/foreach}
                    </table>
                </div>

                {include file="pagination.tpl"}

            </div>
        </div> <!-- div class="row" -->
    </div>
    <!-- div class="container-fluid" -->
    <script type="text/javascript">
		const datetimepicker1 = new tempusDominus.TempusDominus(document.getElementById('datetimepicker1'),{
			localization: {
				locale: '{$language}',
				format: 'yyyy-MM-dd HH:mm:ss',
			}
		});

        const datetimepicker2 = new tempusDominus.TempusDominus(document.getElementById('datetimepicker2'),{
            localization: {
                locale: '{$language}',
                format: 'yyyy-MM-dd HH:mm:ss',
            }
        });
    </script>
{/block}
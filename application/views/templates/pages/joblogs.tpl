{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Job logs{/t}</title>
{/block}

{block name=body}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-3 mb-0">{t}Job logs{/t}</h3>
                <p class="mb-0">{t}Bacula job log{/t}</p>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-3">
                <div class="card">
                    <div class="card-header">{t}Job details{/t}</div>
                    <div class="card-body">
                        <h5 class="card-title">{t}Job id{/t}</h5>
                        <p class="card-text">{$job->jobid}</p>

                        <h5 class="card-title">{t}Job name{/t}</h5>
                        <p class="card-text">{$job->job_name}</p>

                        <h5 class="card-title">{t}Job status{/t}</h5>
                        <p class="card-text">{$job->jobstatuslong}</p>

                        <h5 class="card-title">{t}Job bytes{/t}</h5>
                        <p class="card-text">{$job->getJobBytes()}</p>

                        <h5 class="card-title">{t}Scheduled time{/t}</h5>
                        <p class="card-text">{$job->schedtime}</p>

                        <h5 class="card-title">{t}Job start time{/t}</h5>
                        <p class="card-text">{$job->starttime}</p>

                        <h5 class="card-title">{t}Job end time{/t}</h5>
                        <p class="card-text">{$job->endtime}</p>

                        <h5 class="card-title">{t}Job level{/t}</h5>
                        <p class="card-text">{$job->getLevel()}</p>

                        <h5 class="card-title">{t}Pool{/t}</h5>
                        <p class="card-text">{$job->pool_name}</p>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-condensed table-bordered">
                        <tr>
                            <th class="text-center">{t}Time{/t}</th>
                            <th class="text-center">{t}Event{/t}</th>
                        </tr>
                        {foreach from=$joblogs item=log}
                            <tr>
                                <td class="text-center">{$log->getTime()}</td>
                                <td class="text-left">{$log->getLogText()}</td>
                            </tr>
                            {foreachelse}
                            <tr>
                                <td colspan="2" class="text-center">{t}No log(s) for this job{/t}</td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>
        </div>
    </div>
{/block}
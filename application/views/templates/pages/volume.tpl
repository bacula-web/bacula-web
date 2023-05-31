{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Volume details{/t}</title>
{/block}

{block name=body}
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-3 mb-0">{t}Volume details{/t}</h3>
                <p class="mb-0">{t}Bacula volume details{/t}</p>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-4">

                <div class="card border-dark mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Details</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <b>{t}Media id{/t}:</b> {$volume->mediaid}
                            </li>
                            <li class="list-group-item">
                                <b>{t}Volume name{/t}:</b> {$volume->volumename}
                            </li>
                            <li class="list-group-item">
                                <b>{t}Volume bytes{/t}:</b> {$volume->getVolbytes()}
                            </li>
                            <li class="list-group-item">
                                <b>{t}Volume files{/t}:</b> {$volume->volfiles}
                            </li>
                            <li class="list-group-item">
                                <b>{t}Last written{/t}:</b> {$volume->lastwritten}
                            </li>
                            <li class="list-group-item">
                                <b>{t}Metia Type{/t}:</b> {$volume->mediatype}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-8">

                <div class="card border-dark mb-3">
                    <div class="card-header">
                        <h3 class="card-title">{t}Jobs{/t}</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-condensed table-striped text-center">
                                <thead>
                                <tr>
                                    <th class="text-center">{t}Job ID{/t}</th>
                                    <th class="text-left">{t}Name{/t}</th>
                                    <th class="text-center">{t}Type{/t}</th>
                                </tr>

                                {foreach $jobs as $job}
                                    <tr>
                                        <td>
                                            <a href="/joblog/{$job.jobid}"
                                               title="{t}Show job{/t}">{$job.jobid}</a>
                                        </td>
                                        <td>{$job.name}</td>
                                        <td>{$job.type}</td>
                                    </tr>
                                    {foreachelse}
                                    <tr>
                                        <td colspan="3"
                                            class="text-center">{t}No job(s) associated to this volume{/t}</td>
                                    </tr>
                                {/foreach}
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
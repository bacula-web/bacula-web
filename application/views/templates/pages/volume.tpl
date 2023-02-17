{extends file='default.tpl'}

{block name=title}
	<title>Bacula-Web - {t}Volume details{/t}</title>
{/block}

{block name=body}
<div class="container">

    <div class="page-header">
        <h3>{t}Volume details{/t}<small>&nbsp;{t}Bacula volume details{/t}</small></h3>
    </div>

    <div class="row">
        <div class="col-xs-4">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Details</h3>
                </div>
                <div class="panel-body">
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

        <div class="col-xs-8">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{t}Jobs{/t}</h3>
                </div>
                <div class="panel-body">

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
                                    <a href="index.php?page=joblogs&jobid={$job.jobid}" title="{t}Show job{/t}">{$job.jobid}</a>
                                </td>
                                <td>{$job.name}</td>
                                <td>{$job.type}</td>
                            </tr>
                        {/foreach}
                        </thead>
                    </table>
                </div>
                </div>
        </div>
    </div>


</div> <!-- end div class="container-fluid" -->
{/block}
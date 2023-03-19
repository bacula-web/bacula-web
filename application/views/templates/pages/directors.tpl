{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Directors{/t}</title>
{/block}

{block name=body}
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-3 mb-0">{t}Directors{/t}</h3>
                <p class="mb-0">{t}Bacula director(s) overview{/t}</p>
                <hr>
            </div>
        </div>

        <p>Found {$directors_count} Bacula director catalog(s) in your configuration</p>

        <div class="row">
            {foreach from=$directors key=id item=director}
                <div class="col-4">
                    <div class="card border-dark m-3">
                        <div class="card-body">
                            <h4 class="card-title">{$director.label}</h4>
                            <p class="card-text">{$director.description}</p>
                            <hr>
                            <h5 class="card-title">{t}Client(s){/t}</h5>
                            <p class="card-text">{$director.clients}</p>

                            <h5 class="card-title">{t}Job(s){/t}</h5>
                            <p class="card-text">{$director.jobs}</p>

                            <h5 class="card-title">{t}Total bytes{/t}</h5>
                            <p class="card-text">{$director.totalbytes}</p>

                            <h5 class="card-title">{t}Total files{/t}</h5>
                            <p class="card-text">{$director.totalfiles}</p>

                            <h5 class="card-title">{t}Database size{/t}</h5>
                            <p class="card-text">{$director.dbsize}</p>

                            <h5 class="card-title">{t}Volume(s){/t}</h5>
                            <p class="card-text">{$director.volumes}</p>

                            <h5 class="card-title">{t}Volume(s) size{/t}</h5>
                            <p class="card-text">{$director.volumesize}</p>

                            <h5 class="card-title">{t}Pool(s){/t}</h5>
                            <p class="card-text">{$director.pools}</p>

                            <h5 class="card-title">{t}FileSet(s){/t}</h5>
                            <p class="card-text">{$director.filesets}</p>
                        </div>
                    </div>

                </div>
            {/foreach}
        </div>
    </div>
{/block}
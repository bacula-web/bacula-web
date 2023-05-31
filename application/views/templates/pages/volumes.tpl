{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Volumes report{/t}</title>
{/block}

{block name=body}
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3 class="mt-3 mb-0">{t}Volumes report{/t}</h3>
                <p class="mb-0">{t}Bacula volume(s) overview {/t}</p>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-10">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped text-center">
                        <thead>
                        <tr>
                            <th class="text-center">{t}Volume name{/t}</th>
                            <th class="text-center">{t}Bytes{/t}</th>
                            <th class="text-center">{t}Files{/t}</th>
                            <th class="text-center">{t}Jobs{/t}</th>
                            <th class="text-center">{t}Media Type{/t}</th>
                            <th class="text-center">{t}Pool{/t}</th>
                            <th class="text-center">{t}Expire{/t}</th>
                            <th class="text-center">{t}Last written{/t}</th>
                            <th class="text-center">{t}Status{/t}</th>
                            <th class="text-center">{t}Slot{/t}</th>
                            <th class="text-center">{t}In Changer{/t}</th>
                        </tr>
                        </thead>

                        {foreach from=$volumes item=volume name=volumes}
                            <tr>
                                <td>
                                    <a href="/volumes/{$volume.mediaid}"
                                       title="{t}Show volume{/t}">{$volume.volumename}</a>
                                </td>
                                <td class="text-right">{$volume.volbytes}</td>
                                <td class="text-right">{$volume.volfiles}</td>
                                <td class="text-right">{$volume.voljobs}</td>
                                <td>{$volume.mediatype}</td>
                                <td>{$volume.pool_name}</td>
                                <td>{$volume.expire}</td>
                                <td>{$volume.lastwritten}</td>
                                <td title="{$volume.volstatus}">
                                    <i class="fa {$volume.status_icon}"></i>
                                </td>
                                <td>{$volume.slot}</td>
                                <td>{$volume.inchanger}</td>
                            </tr>
                            {foreachelse}
                            <tr>
                                <td colspan="10" class="text-center">
                                    {t}No volume(s) to display{/t}
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div> <!-- end div class="table-responsive" -->

                {include file="pagination.tpl"}
            </div>
            <div class="col-2">
                <!-- Options -->
                <!-- <form class="row row-cols-lg-auto g-3 align-items-center align-items-end" action="index.php?page=volumes" method="post"> -->
                <form class="" action="/volumes" method="post">
                    <!-- Pools -->
                    <div class="mb-3">
                        <label class="form-label" for="filter_pool_id">{t}Pool{/t}</label>
                        {html_options class="form-select form-select-sm" name=filter_pool_id options=$pools_list selected=$pool_id}
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="inputOrderBy">{t}Order by{/t}</label>
                        {html_options class="form-select form-select-sm" name=filter_orderby id="inputOrderBy" options=$orderby selected=$orderby_selected}
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="filter_orderby_asc"
                                   id="filter_orderby_asc" value="{t}Asc{/t}" {$orderby_asc_checked}>
                            <label class="form-check-label" for="filter_orderby_asc">
                                {t}Asc{/t}
                            </label>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="filter_inchanger"
                                       name="filter_inchanger" {$inchanger_checked}>
                                <label class="form-check-label" for="filter_inchanger">
                                    {t}In changer{/t}
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary btn-sm"
                                    title="{t}Apply filter and options{/t}">{t}Apply{/t}</button>
                        </div>

                </form>
            </div>
        </div>
    </div>
    <hr/>
    <!-- Found volumes footer -->
    <div class="card border-dark m-3">
        <div class="card-header">{t}Result{/t}</div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <span class="badge">{$volumes_count}</span>
                    {t}Volume(s) found{/t}
                </li>
                <li class="list-group-item">
                    <span class="badge">{$volumes_total_bytes}</span>
                    {t}Total bytes{/t}
                </li>
            </ul>
        </div>
    </div>
{/block}
{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Test{/t}</title>
{/block}

{block name=body}
    <div class="container-fluid" id="testpage">

        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-3 mb-0">{t}Test page{/t}</h3>
                <p class="mb-0">{t}Check requirements and configuration{/t}</p>
                <hr>
            </div>
        </div>

        <table class="table table-striped">
            <tr>
                <th class="text-center">Status</th>
                <th class="text-center">Component</th>
                <th class="text-center">Description</th>
            </tr>
            {foreach from=$checks item=check}
                <tr>
                    <td class="text-center"><span class="{$check.check_result}"></span></td>
                    <td>{$check.check_label}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-toggle="tooltip" data-bs-placement="left"
                                data-bs-custom-class="custom-tooltip"
                                data-bs-title="{$check.check_descr}">
                                <i class="fa-solid fa-info"></i>
                        </button>
                    </td>
                </tr>
            {/foreach}
        </table>

        <!-- Graph testing -->
        <h4>{t}Graph capabilities{/t}</h4>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div id="{$pie_graph_id}">
                    <svg></svg>
                </div>
                {$pie_graph}
            </div>
            <div class="col-xs-12 col-sm-6">
                <div id="{$bar_chart_id}">
                    <svg></svg>
                </div>
                {$bar_chart}
            </div>
        </div>

    </div>
    <!-- div class="container" -->
{/block}
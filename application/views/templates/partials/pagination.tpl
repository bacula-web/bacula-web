<hr />
<div class="row">
    <div class="col-xs-6">
        <p class="pagination">{$pagination_range} / {$count} row(s) out of {$rowcount}</p>
    </div>
    <div class="col-xs-6">
        <nav aria-label="">
            <ul class="pagination">
                <li class="{$first}">
                {if $first eq "disabled"}
                    <span>
                        <span title="{t}First page{/t} aria-hidden="true">&laquo;</span>
                    </span>
                {else}
                    <a href="{$pagination_link}&pagination_page=1" aria-label="First">
                        <span aria-hidden="true" title="{t}First page{/t}">&laquo;</span>
                    </a>
                {/if}
                </li>

                <li class="{$previous_enabled}">
                    {if $previous_enabled eq "disabled"}
                        <span>
                            <span title="{t}Previous page{/t} aria-hidden="true">&lang;</span>
                        </span>
                    {else}
                        <a href="{$pagination_link}&pagination_page={$previous}" aria-label="Previous">
                            <span aria-hidden="true" title="{t}Previous page{/t}">&lang;</span>
                        </a>
                    {/if}
                </li>
                {* pagination current page is on last 4 *}
                {if $pagination_current lte ($pagination_max-4) }
                    {assign var="pagination_start" value=$pagination_current}
                    {assign var="pagination_end" value=$pagination_current+3}
                {* there is only 1 pagination page *}
                {elseif $pagination_max eq 1}
                    {assign var="pagination_start" value=1}
                    {assign var="pagination_end" value=1}
                {elseif $pagination_max lte 4}
                    {assign var="pagination_start" value=1}
                    {assign var="pagination_end" value=$pagination_max}
                {else}
                        {assign var="pagination_start" value=$pagination_max-3}
                        {assign var="pagination_end" value=$pagination_max}
                {/if}

                {for $page=$pagination_start to $pagination_end}
                    {if $page eq $pagination_current}
                        <li class="active">
                            <span>{$page} <span class="sr-only">(current)</span></span>
                        </li>
                    {else}
                         <li>
                            <a href="{$pagination_link}&pagination_page={$page}">{$page}</a>
                         </li>
                    {/if}
                {/for}

                <li class="{$next_enabled}">
                {if $next_enabled eq "disabled"}
                    <span>
                        <span title="{t}Next page{/t} aria-hidden="true">&rang;</span>
                    </span>
                {else}
                    <a href="{$pagination_link}&pagination_page={$next}" aria-label="Next">
                        <span aria-hidden="true" title="{t}Next page{/t}">&rang;</span>
                    </a>
                {/if}
                </li>

                <li class="{$last}">
                {if $last eq "disabled"}
                    <span>
                        <span title="{t}Last page{/t} aria-hidden="true">&raquo;</span>
                    </span>
                {else}
                    <a href="{$pagination_link}&pagination_page={$pagination_max}" aria-label="">
                            <span aria-hidden="true" title="{t}Last page{/t}">&raquo;</span>
                    </a>
                {/if}
                </li>
            </ul>
        </nav>
    </div>
</div>
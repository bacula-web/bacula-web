<div class="row">
    <div class="col-xs-6">
        <p class="pagination">{$pagination_range} / {$count} row(s) out of {$rowcount}</p>
    </div>
    <div class="col-xs-6">
        <nav aria-label="pagination">
            <ul class="pagination justify-content-center">
                <li class="page-item {$first}">
                {if $first eq "disabled"}
                    <span title={{ 'First page'|trans }} class="page-link" aria-hidden="true">&laquo;</span>
                {else}
                    <a class="page-link" href="{$pagination_link}&pagination_page=1" aria-label="First">&laquo;</a>
                {/if}
                </li>

                <li class="page-item {$previous_enabled}">
                    {if $previous_enabled eq "disabled"}
                        <span class="page-link" title="{{ 'Previous page'|trans }} aria-hidden="true">&lang;</span>
                    {else}
                        <a class="page-link" href="{$pagination_link}&pagination_page={$previous}" title="{{ 'Previous page'|trans }}" aria-label="Previous">&lang;</a>
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
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{$page}</span>
                        </li>
                    {else}
                         <li class="page-item">
                            <a class="page-link" href="{$pagination_link}&pagination_page={$page}">{$page}</a>
                         </li>
                    {/if}
                {/for}

                <li class="page-item {$next_enabled}">
                {if $next_enabled eq "disabled"}
                    <span class="page-link" title="{{ 'Next page'|trans }}" aria-hidden="true">&rang;</span>
                {else}
                    <a class="page-link" href="{$pagination_link}&pagination_page={$next}" title="{{ 'Next page'|trans }}" aria-label="Next">&rang;</a>
                {/if}
                </li>

                <li class="page-item {$last}">
                {if $last eq "disabled"}
                    <span class="page-link" title="{{ 'Last page'|trans }}" aria-hidden="true">&raquo;</span>
                {else}
                    <a class="page-link" href="{$pagination_link}&pagination_page={$pagination_max}" title="{{ 'Last page'|trans }}" aria-label="">&raquo;</a>
                {/if}
                </li>
            </ul>
        </nav>
    </div>
</div>



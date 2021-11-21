<hr />
<div class="row">
    <div class="col-xs-6">
        <p class="pagination">{$pagination_range} / {$count}</p>
    </div>
    <div class="col-xs-6">
        <nav aria-label="">
            <ul class="pagination">
                <li class="{$first}">
                    <a href="{$pagination_link}&pagination_page=1" aria-label="First">
                        <span aria-hidden="true" title="{t}First page{/t}">&laquo;</span>
                    </a>
                </li>

                <li class="{$previous_enabled}">
                    <a href="{$pagination_link}&pagination_page={$previous}" aria-label="Previous">
                        <span aria-hidden="true" title="{t}Previous pages{/t}">&lang;</span>
                    </a>
                </li>
                    {if $pagination_current lt $pagination_max-4}
                        {assign var="pagination_start" value=$pagination_current}
                        {assign var="pagination_end" value=$pagination_current+4}
                    {else}
                        {assign var="pagination_start" value=$pagination_max-4}
                        {assign var="pagination_end" value=$pagination_max}
                    {/if}
                    {for $page=$pagination_start to $pagination_end}
                        {if $page eq $pagination_current}
                            <li class="active">
                            <a href="">{$page}
                             <span class="sr-only">(current)</span>
                           </a>
                        {else}
                         <li>
                         <a href="{$pagination_link}&pagination_page={$page}">{$page}
                             <span class="sr-only">(current)</span>
                         </a>
                        {/if}
                      </li>                    
                   {/for}

                <li class="{$next_enabled}">
                    <a href="{$pagination_link}&pagination_page={$next}" aria-label="Next">
                        <span aria-hidden="true" title="{t}Next pages{/t}">&rang;</span>
                    </a>
                </li>

                <li class="{$last}">
                    <a href="{$pagination_link}&pagination_page={$pagination_max}" aria-label="">
                            <span aria-hidden="true" title="{t}Last page{/t}">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
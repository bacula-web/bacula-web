{if isset($flash) }
    {if is_array($flash) }
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {$flash[0]}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    {/if}
{/if}

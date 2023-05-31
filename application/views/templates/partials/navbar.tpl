<nav class="navbar navbar-expand-md navbar-dark bg-dark" aria-label="Fourth navbar example">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <img src="/img/bacula-web-logo.png" alt="Bacula-Web logo" width="22" height="24" class="d-inline-block align-top">
            {$app_name}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarcollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/">{t}Dashboard{/t}</a>
                </li>

                {if $user_authenticated eq 'yes' or $enable_users_auth eq false }
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">{t}Reports{/t}</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/jobs">{t}Jobs{/t}</a></li>
                            <li><a class="dropdown-item" href="/pools">{t}Pools{/t}</a></li>
                            <li><a class="dropdown-item" href="/volumes">{t}Volumes{/t}</a></li>
                            <li><a class="dropdown-item" href="/backupjob">{t}Backup job{/t}</a></li>
                            <li><a class="dropdown-item" href="/client">{t}Client{/t}</a></li>
                            <li><a class="dropdown-item" href="/directors">{t}Director(s){/t}</a></li>
                        </ul>
                    </li>
                {/if}
            </ul>

            <div class="d-lg-flex col-lg-3 justify-content-lg-end">
                <ul class="navbar-nav">
                    {if $user_authenticated eq 'yes' or $enable_users_auth eq false }
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-database"></i> {$catalog_label}</a>
                            <ul class="dropdown-menu">
                                {foreach from=$catalogs key=catalog_id item=catalog_name}
                                    <li>
                                        <a class="dropdown-item" href="index.php?catalog_id={$catalog_id}{if isset($page) }&page={$page}{/if}">
                                            {if $catalog_id eq $catalog_current_id}
                                                <i class="fa fa-check fa-fw"></i>
                                            {else}
                                                <i class="fa fa-fake fa-fw"></i>
                                            {/if}{$catalog_name}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </li>
                    {/if}
                </ul>

                <ul class="navbar-nav">
                {if isset($user_authenticated) }
                    {if $enable_users_auth eq 'true' and $user_authenticated eq 'yes' }
                        <li class="nav-item dropdown dropstart">

                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-user"></i> {$username}</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="/user" title="User settings">
                                        <i class="fa fa-wrench fa-fw"></i> {t}User settings{/t}
                                    </a>
                                </li>
                                <li>
                                    <form class="navbar-form navbar-left" action="/signout" method="POST">
                                        <input class="form-control" type="hidden" name="action" value="logout">
                                        <button type="submit" class="btn btn-sm btn-light ms-2" title="Sign out">
                                            <i class="fa fa-sign-out fa-lg"></i> {t}Sign out{/t}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    {/if}
                {/if}
                </ul>

                <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fa-solid fa-gear"></i></button>
            </div>

        </div>

    </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">{t}About{/t}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <h6>{t}Settings{/t}</h6>

        <a class="btn btn-primary m-1 w-100" href="/settings" title="Settings"><i class="fa fa-cogs fa-fw"></i> {t}Settings{/t}</a>

        <a class="btn btn-success m-1 w-100" href="/test" title="Display the test page"><i class="fa fa-wrench fa-fw"></i> {t}Test page{/t}</a>

        <hr>

        <h6>{t}Help{/t}</h6>

        <a class="btn btn-light m-1 w-100" href="https://www.bacula-web.org" title="Visit the official web site" target="_blank"
           rel="noopener noreferrer"><i class="fa fa-globe fa-fw"></i> {t}Official web site{/t}</a>

        <a class="btn btn-light m-1 w-100" href="https://github.com/bacula-web/bacula-web/issues"
               title="Bug and feature request tracker" target="_blank" rel="noopener noreferrer"><i
                        class="fa fa-bug fa-fw"></i> {t}Bug tracker{/t}</a>

        <a class="btn btn-light m-1 w-100" href="https://docs.bacula-web.org/en/latest/" title="Documentation" target="_blank"
           rel="noopener noreferrer"><i class="fa fa-book fa-fw"></i> {t}Documentation{/t}</a>

        <a class="btn btn-light m-1 w-100" href="https://github.com/bacula-web/bacula-web" title="Bacula-Web project on GitHub" target="_blank" rel="noopener noreferrer">
            <i class="fa-brands fa-github"></i> {t}Project on GitHub{/t}
        </a>

        <hr>

        <h6>{t}Version{/t}</h6>
        <button type="button" class="btn btn-outline-secondary w-100" disabled>{$app_name} {$app_version}</button>

    </div>
</div>

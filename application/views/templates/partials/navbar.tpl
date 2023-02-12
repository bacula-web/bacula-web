<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">{$app_name}</a>
        </div> <!-- div class="navbar-header" -->

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            {if isset($user_authenticated) }
                {if $user_authenticated eq 'yes' or $enable_users_auth eq 'false' }
                    <!-- Reports dropdown menu -->
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                        class="fa fa-file-text-o fa-fw"></i> {t}Reports{/t} <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="index.php?page=jobs">{t}Jobs{/t}</a></li>
                                <li><a href="index.php?page=pools">{t}Pools{/t}</a></li>
                                <li><a href="index.php?page=volumes">{t}Volumes{/t}</a></li>
                                <li><a href="index.php?page=backupjob">{t}Backup job{/t}</a></li>
                                <li><a href="index.php?page=client">{t}Client{/t}</a></li>
                                <li><a href="index.php?page=directors">{t}Director(s){/t}</a></li>
                            </ul>
                        </li>
                    </ul>
                {/if}
            {/if}

            <ul class="nav navbar-nav navbar-right">
                {if isset($user_authenticated) }
                    {if $user_authenticated eq 'yes' or $enable_users_auth eq 'false' }
                        <!-- Catalog selector -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                        class="fa fa-database"></i> {$catalog_label} <span class="caret"></span></a>

                            <ul class="dropdown-menu">
                                {foreach from=$catalogs key=catalog_id item=catalog_name}
                                    <li>
                                        <a href="index.php?catalog_id={$catalog_id}{if isset($page) }&page={$page}{/if}">

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
                    <!-- end Catalog selector -->
                {/if}

                <!-- Authenticated user options -->
                {if isset($user_authenticated) }
                    {if $enable_users_auth eq 'true' and $user_authenticated eq 'yes' }
                        <li class="dropdown">
                            <a href="#" class="dropdown-toogle" data-toggle="dropdown" role="button">{$username} <i
                                        class="fa fa-user fa-fw"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="index.php?page=usersettings" title="User settings"> <i
                                                class="fa fa-wrench fa-fw"></i> {t}User settings{/t}</a></li>
                            </ul>
                        </li>
                        <form class="navbar-form navbar-left" action="index.php?page=login" method="POST">
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="btn btn-link" title="Sign out">
                                <i class="fa fa-sign-out fa-lg"></i>
                            </button>
                            <!-- <button type="submit" class="btn btn-default">Submit</button> -->
                        </form>
                    {/if}
                {/if}

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-cog fa-fw"></i> <span
                                class="hidden-sm hidden-md hidden-lg">{t}About{/t}</span></a>
                    <ul class="dropdown-menu">
                        {if isset($user_authenticated) }
                            {if $user_authenticated eq 'yes' or $enable_users_auth eq 'false' }
                                <li role="presentation" class="dropdown-header">Tools</li>
                                <li>
                                    <a href="index.php?page=settings" title="Settings"><i
                                                class="fa fa-cogs fa-fw"></i> {t}Settings{/t}</a>
                                </li>
                                <li>
                                    <a href="index.php?page=test" title="Display the test page"><i
                                                class="fa fa-wrench fa-fw"></i> {t}Test page{/t}</a>
                                </li>
                                <li role="presentation" class="divider"></li>
                            {/if}
                        {/if}
                        <li role="presentation" class="dropdown-header">Help</li>
                        <li>
                            <a href="https://docs.bacula-web.org/en/latest/" title="Documentation" target="_blank"
                               rel="noopener noreferrer"><i class="fa fa-book fa-fw"></i> {t}Documentation{/t}</a>
                        </li>
                        <li>
                            <a href="https://www.bacula-web.org" title="Visit the official web site" target="_blank"
                               rel="noopener noreferrer"><i class="fa fa-globe fa-fw"></i> {t}Official web site{/t}</a>
                        </li>
                        <li>
                            <a href="https://github.com/bacula-web/bacula-web/issues"
                               title="Bug and feature request tracker" target="_blank" rel="noopener noreferrer"><i
                                        class="fa fa-bug fa-fw"></i> {t}Bug tracker{/t}</a>
                        </li>
                        <li><a href="https://github.com/bacula-web/bacula-web" title="Bacula-Web project on GitHub"
                               target="_blank" rel="noopener noreferrer">
                                <i class="fa fa-github fa-fw"></i>{t}Project on GitHub{/t}</a>
                        </li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation" class="dropdown-header">{t}Version{/t}</li>
                        <li class="disabled"><a href="#"><i class="fa fa-info fa-fw"></i> {$app_name} {$app_version}</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div> <!-- div class="collapse navbar-collapse"-->
    </div> <!-- div class="container-fluid" -->
</div> <!-- class="navbar" -->

{if isset($user_authenticated) }
    {if $user_authenticated eq 'yes' or $enable_users_auth eq 'false' }
        <div class="container-fluid">
            <!-- Breadcrumb -->
            <div class="row">
                <div class="col-xs-12">
                    <ol class="breadcrumb">
                        {$breadcrumb}
                    </ol>
                </div> <!-- div class="col..." -->
            </div> <!-- div class="row" -->
        </div>
        <!-- div class="container" -->
    {/if}
{/if}

{if $userAlert != ''}
<div class="container">
    <div class="alert alert-{$userAlertType} role=" alert
    ">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    {$userAlert}
</div>
{/if}
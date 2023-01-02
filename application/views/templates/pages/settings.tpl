{extends file='default.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Settings{/t}</title>
{/block}

{block name=body}
<div class="container">

  <div class="page-header">
    <h3>{$page_name} <small></small></h3>
  </div>

  <div class="row">

        <div class="col-xs-3">

            <!-- required for floating -->
            <!-- Nav tabs -->
            <ul class="nav nav-tabs tabs-left nav-stacked">
                <li class="active"><a href="#general" data-toggle="tab">General settings</a></li>
                <li><a href="#users" data-toggle="tab">Users</a></li>
            </ul>
        </div>
        <div class="col-xs-8">
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="general"> <h4>General settings</h4> 

                  <br />

                  <form class="form-horizontal">

                    <!-- Date / Time format -->
                    <div class="form-group">
                      <label class="col-xs-4 control-label" for="config_datetime_format">Date / Time format</label>
                      <div class="col-xs-8">
                        <input type="text" class="form-control" id="config_datetime_format" value="{$config_datetime_format}" readonly>
                      </div>
                    </div> <!-- end div class=form-group -->

                    <!-- Language -->
                    <div class="form-group">
                      <label class="col-xs-4 control-label" for="config_language">Language</label>
                      <div class="col-xs-8">
                        <input type="text" class="form-control" id="config_language" value="{$config_language}" readonly>
                      </div>
                    </div> <!-- end div class=form-group -->

                    <!-- Show inactive clients -->
                    <div class="form-group">
                      <label class="col-xs-4 control-label" for="config_show_inactive_clients">Show inactive clients</label>
                      <div class="col-xs-8">
                        <input type="checkbox" id="config_show_inactive_clients" {$config_show_inactive_clients} disabled>
                      </div>
                    </div> <!-- end div class=form-group -->

                    <!-- Hide empty pools -->
                    <div class="form-group">
                      <label class="col-xs-4 control-label" for="config_hide_empty_pools">Hide empty pools</label>
                      <div class="col-xs-8">
                        <input type="checkbox" id="config_hide_empty_pools" {$config_hide_empty_pools} disabled>
                      </div>
                    </div> <!-- end div class=form-group -->

                    <!-- Enable users authentication -->
                    <div class="form-group">
                      <label class="col-xs-4 control-label" for="config_enable_users_auth">Users authentication</label>
                      <div class="col-xs-8">
                        <input type="checkbox" id="config_eanble_users_auth" {$config_enable_users_auth} disabled>
                      </div>
                    </div> <!-- end div class=form-group -->

                    <!-- Debug mode -->
                    <div class="form-group">
                      <label class="col-xs-4 control-label" for="config_debug">Debug mode</label>
                      <div class="col-xs-8">
                        <input type="checkbox" id="config_debug" {$config_debug} disabled>
                      </div>
                    </div> <!-- end div class=form-group -->

                  </form>
                </div>

                <!-- Users tab -->
                <div class="tab-pane" id="users"> 
               
                  <h4>Users</h4>

                  <div class="table-responsive">
                    <table class="table table-condensed table-striped">
                      <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                      </tr>
                      {foreach from=$users item=user}
                      <tr>
                        <td>{$user->getUsername()}</td>
                        <td>{$user->getEmail()}</td>
                        <td></td>
                      </tr>
                      {/foreach}
                    </table>
                  </div> <!-- end div class=table... -->

                <hr>

                <h4>Add user</h4>

                <form action="index.php?page=settings" method="post" data-toggle="validator">
                  <label for="inputUsername">Username</label>
                  <input name="username"  type="text" id="inputUsername" class="form-control" placeholder="username" aria-describedby="username_helpblock" required>

                  <label for="inputEmail">Email</label>
                  <input name="email" value="" type="email" id="inputEmail" class="form-control" placeholder="email address" data-error="Invalid email address"required>
                  <div class="help-block with-errors"></div>

                  <label for="password">Password</label>
                  <input name="password" type="password" id="inputpassword" class="form-control" placeholder="password" data-minlength="6" required>
                  <div class="help-block">Password must be at least 6 characters</div>                  
        
                  <input type="hidden" name="action" value="createuser">
                  
                  </br>
        
                  <button class="btn btn-sm btn-primary pull-right" type="submit">Create</button>
                </form>

                </div> <!-- end div class=tab-pane -->

        </div> <!-- end div class="tab-content -->

      <div class="clearfix"></div>
    </div> <!-- div class=col... -->

  </div> <!-- end div class=row -->

</div> <!-- end div class=container -->
{/block}
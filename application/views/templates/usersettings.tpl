<div class="container">

  <div class="page-header">
    <h3>{$page_name} <small></small></h3>
  </div>

  <div class="row">
    <div class="col col-xs-6">

      {if $userAlert != ''}
      <div class="alert alert-{$userAlertType} role="alert">
        {$userAlert}
      </div>
      {/if}

      <h4>User details</h4>

      <form action="index.php?page=usersettings" method="post">
        </br>
        <label for="inputUsername">Username</label>
        <input name="username" value="{$username}" type="text" id="inputUsername" class="form-control" placeholder="Username" aria-describedby="username_helpblock" disabled>
        <span id="username_helpblock" class="help-block"><i>Username can't be changed</i></span>

        <label for="inputEmail">Email</label>
        <input name="email" value="{$email}" type="email" id="inputEmail" class="form-control" placeholder="Email address" readonly>
        <span id="email_helpblock" class="help-block"><i>This will come in a next version ;)</i></span>
        </br>
        <button class="btn btn-sm btn-primary pull-right" disabled="disabled" type="submit">Update</button>
      </form>

    </div>
  </div>
  <div class="row">
    <div class="col col-xs-4">

      <h4>Password management</h4>

      <form action="index.php?page=usersettings" method="post" data-toggle="validator">
        <div class="form-group"> 
          <label for="currentpass">Current password</label>
          <input name="oldpassword" type="password" id="oldpassword" class="form-control" placeholder="Current password" aria-describedby="currentpass" required>
          <span id="currentpass" class="help-block"><i>Your current password (required)</i></span>

          <label for="newpass">New password</label>
          <input name="newpassword" type="password" id="inputpassword" class="form-control" placeholder="New password" data-minlength="6" required>
          <div class="help-block">Password must be at least 6 characters</div>
          <label for="inputUsername">Confirm</label>
          <input name="confnewpassword" value="" type="password" data-minlength="6" data-match="#inputpassword" data-match-error="Passwords don't match" id="inputconfpassword" class="form-control" placeholder="Confirm new password" required>
         
          <div class="help-block"></div>

          <input type="hidden" name="action" value="passwordreset">

          <br />
          <button class="btn btn-sm btn-primary pull-right" type="submit">Reset password</button>
        </div> <!-- end div class="form-group" -->
      </form>
    </div> <!-- end div class=colxx -->
  </div> <!-- end div class=row-->
</div> <!-- end div class=container -->

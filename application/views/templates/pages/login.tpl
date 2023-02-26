{extends file='default.tpl'}

{block name=title}
  <title>Bacula-Web - {t}Login{/t}</title>
{/block}

{block name=body}
<div class="container">
  <div class="row">
    <div class="col-xs-4 col-xs-offset-4">
      <div class="jumbotron form-signin">
        <img class="img-responsive center-block" src="img/bacula-web-logo.png" alt="" />

        <form action="index.php?page=login" method="POST">
          <h4>Please sign in</h4>
        
          <label for="inputUsername" class="sr-only">Username</label>
          <input name="username" type="text" id="inputUsername" class="form-control" placeholder="Username" required autofocus>
          <label for="inputPassword" class="sr-only">Password</label>
          <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
          <input type="hidden" name="action" value="login">

          </br>

          <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>
      </div>
    </div>
  </div>
</div> <!-- end div class=container -->
{/block}
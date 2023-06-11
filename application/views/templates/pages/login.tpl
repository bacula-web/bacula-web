{extends file='blank.tpl'}

{block name=title}
    <title>Bacula-Web - {t}Login{/t}</title>
{/block}

{block name=body}

    {if isset($flash) }
        {if is_array($flash) }
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {$flash[0]}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        {/if}
    {/if}

    <div class="bg-light min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-group d-block d-md-flex row">
                        <div class="card col-md-7 p-4 mb-0">
                            <div class="card-body">
                                <h1>Login</h1>
                                <p class="text-medium-emphasis">Please sign in</p>
                                <form action="/login" method="post">
                                    <div class="input-group mb-3">
                  <span class="input-group-text">
                    <i class="cil-user"></i>&nbsp;
                    <input class="form-control" type="text" name="username" id="inputUsername" placeholder="Username" autofocus
                           required>
                                    </div>
                                    <div class="input-group mb-4"><span class="input-group-text">
                    <i class="cil-lock-locked"></i>&nbsp;
                    <input class="form-control" type="password" name="password" id="inputPassword"
                           placeholder="Password" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <button class="btn btn-primary px-4" type="submit">Login</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card col-md-5 text-white bg-light py-5">
                            <div class="card-body text-center">
                                <div>
                                    <img class="img-responsive" src="img/bacula-web-logo.png" alt="Bacula-Web logo"/>
                                    <div>
                                        <h2 class="text-black">{$app_name}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
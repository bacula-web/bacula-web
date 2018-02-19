<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2018, Davide Franco	                                    |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
 */

class WebApplication 
{
    protected $name;
    protected $version;
    protected $view;
    protected $defaultView;
    protected $userauth;

    protected function setup()
    {
        // Start user session
        session_start();

        // Prepare users authentication back-end
        $this->userauth = new UserAuth();

        // Check if database exists and is writable
        $this->userauth->checkSchema();

        // Check application config file
        $appConfigFile = CONFIG_DIR . 'application.php';
        if( file_exists($appConfigFile) && is_readable($appConfigFile) ) {
            require_once($appConfigFile);
        }else{
            throw new Exception('Application config file not found, please fix it');
        }

        // Set application properties from config file        
        $this->name = $app['name'];
        $this->version = $app['version'];
        $this->defaultView = $app['defaultview']; 

        if(isset($_REQUEST['action'])) {
            switch($_REQUEST['action']) {
            case 'login':
                $_SESSION['user_authenticated'] = $this->userauth->authUser($_POST['username'], $_POST['password']);

                if( $_SESSION['user_authenticated'] == 'yes') {
                    $user = $this->userauth->getData( filter_input( INPUT_POST, 'username', FILTER_SANITIZE_STRING));
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];

                    $this->view = new $this->defaultView();
                    $this->view->assign('user_authenticated', 'yes');
                }
                break;
            case 'logout':
                $_SESSION['user_authenticated'] = 'no';
                $this->userauth->destroySession();
            }
        }    

        // Check if user is authenticated
        if( $_SESSION['user_authenticated'] == 'yes') {

            // Get requested page or set default one
            if(isset($_REQUEST['page'])) {
                $pageName = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING);

                // Check if requested page is a known route
                if( array_key_exists($pageName, $app['routes'])) {
                    $viewName = ucfirst($app['routes'][$pageName]) . 'View';

                    if(class_exists($viewName)) {
                        $this->view = new $viewName;
                    }else {
                        throw new Exception("PHP class $viewName not found");
                    }
                }else {
                    throw new Exception('Requested page does not exist');
                }
            }else{
                $this->view = new $this->defaultView();
            }

            $this->view->assign('user_authenticated', 'yes');

        }else{
            // user not authenticated, moving to login page
            $this->view = new LoginView();
        }
    }

    public function run() {
        try{
            $this->setup();
            $this->init();
            $this->view->prepare();
        }catch( Exception $e) {
            // Display application error here
            CErrorHandler::displayError($e);
            // Render the view
        }finally {
            $this->view->render();
        }
    }
}

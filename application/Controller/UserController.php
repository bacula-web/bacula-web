<?php

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of Bacula-Web.
 *
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see
 * <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Core\User;
use App\Libs\Config;
use Core\App\UserAuth;
use Core\Controller\AbstractController;
use Core\Db\ManagerRegistry;
use Core\Exception\ValidationException;
use Core\Helpers\Sanitizer;
use GuzzleHttp\Psr7\Response;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

class UserController extends AbstractController
{
    /**
     * @var string
     */
    protected string $username = '';

    /**
     * @var UserAuth
     */
    private UserAuth $userAuth;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param Twig $view
     * @param Config $config
     * @param SessionInterface $session
     * @param UserAuth $userAuth
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        Twig $view,
        Config $config,
        SessionInterface $session,
        UserAuth $userAuth
    ) {
        parent::__construct($managerRegistry, $view, $config, $session);

        $this->userAuth = $userAuth;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function prepare(Request $request, Response $response): Response
    {
        $em = $this->managerRegistry->getManager();
        $repository = $em->getRepository(User::class);

        $tplData = [];
        $postData = $request->getParsedBody();

        /**
         * @var User $user
         */
        $user = $repository->findOneBy(['username' => $this->session->get('username')]);

        $tplData['username'] = $user->getUsername();
        $tplData['email'] = $user->getEmail();

        // TODO: refactor below code to a proper Controller::action
        // Check if password reset have been requested
        if (isset($postData['action'])) {
            switch (Sanitizer::sanitize($postData['action'])) {
                case 'passwordreset':
                    $validator = new Validator($postData, [
                        'username',
                        'oldpassword',
                        'newpassword',
                        'confnewpassword']);
                    $validator
                        ->rule(function ($field, $value, $params, $fields) use ($user) {
                            if (($this->userAuth->authUser($user->getUsername(), $fields['oldpassword']) == 'yes')) {
                                return true;
                            }
                            return false;
                        }, "oldpassword")->message("Your current password is not valid")
                        ->rule('equals', 'newpassword', 'confnewpassword')->message('Both passwords must match')
                        ->rule('lengthMin', 'newpassword', 8)->message('Password must be at least 8 characters long');
                    if (! $validator->validate()) {
                        throw new ValidationException($validator->errors());
                    }

                    // Reset password
                    $user->setPassword($postData['newpassword']);
                    $em->persist($user);
                    $em->flush();

                    $this->session->getFlash()->set('info', ['Password successfully updated']);
                    break;
            }
        }

        return $this->view->render($response, 'pages/usersettings.html.twig', $tplData);
    }
}

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
use Core\App\UserAuth;
use Core\Exception\ValidationException;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route("/user", name: "user.profile")]
    public function index(/* Response $response */): Response
    {
        return new Response('user.profile');

        $user = $this->managerRegistry
            ->getManager()
            ->getRepository(User::class)
            ->findOneBy(['username' => $this->session->get('username')]);

        return $this->view->render(
            $response,
            'pages/usersettings.html.twig',
            compact('user')
        );
    }

    /**
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route("/user/reset-password", name: "user.password-reset")]
    public function passwordReset(): Response
    {
        return new Response('User password reset');

        return $this->view->render($response, 'pages/user-passsword-reset.html.twig');
    }

    /**
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route("/user/reset-password", name: "user.attempt-password-reset", methods: ["POST"])]
    public function attemptPasswordReset(/* Request $request, Response $response, UserAuth $userAuth */): Response
    {
        $em = $this->managerRegistry->getManager();
        $repository = $em->getRepository(User::class);

        $postData = $request->getParsedBody();

        $user = $repository->findOneBy(['username' => $this->session->get('username')]);

        $validator = new Validator($postData, [
            'username',
            'oldpassword',
            'newpassword',
            'confnewpassword']);
        $validator
            ->rule(function ($field, $value, $params, $fields) use ($userAuth, $user) {
                if (($userAuth->authUser($user->getUsername(), $fields['oldpassword']) == 'yes')) {
                    return true;
                }
                return false;
            }, "oldpassword")->message("Your current password is not valid")
            ->rule('equals', 'newpassword', 'confnewpassword')->message('Both passwords must match')
            ->rule('lengthMin', 'newpassword', 8)->message('Password must be at least 8 characters long')
            ->rule('different', 'newpassword', 'oldpassword')->message('New password must be different the current one')
        ;

        if (! $validator->validate()) {
            throw new ValidationException($validator->errors());
        }

        // Reset password
        $user->setPassword($postData['newpassword']);
        $em->persist($user);
        $em->flush();

        $this->session->getFlash()->set('info', ['Password successfully updated']);

        // TODO: refactor this into AbstractController
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $relativeUrl = $routeParser->relativeUrlFor('user.attempt-password-reset');

        return $response
            ->withHeader('Location', $relativeUrl)
            ->withStatus(302);
    }
}

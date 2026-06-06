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
use Core\Exception\AppException;
use Core\Exception\ValidationException;
use Core\Helpers\Sanitizer;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SettingsController extends AbstractController
{
    /**
     * @param ParameterBagInterface $parameterBag
     * @return Response
     */
    #[Route("/settings", name: "settings", methods: ["GET"])]
    public function index(ParameterBagInterface $parameterBag): Response
    {
        return $this->render('pages/settings.html.twig', [
            'config_datetime_format' => $parameterBag->get("app.datetime_format"),
            'config_datetime_format_short' => $parameterBag->get("app.datetime_format_short"),
            'config_language' => $parameterBag->get("app.language"),
            'config_show_inactive_clients' => $parameterBag->get("app.show_inactive_clients"),
            'config_hide_empty_pools' => $parameterBag->get("app.hide_empty_pools"),
            'config_debug' => $parameterBag->get("app.debug"),
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     * @throws AppException
     * @throws ORMException
     */
    #[Route("/settings", name: "adduser", methods: ["POST"])]
    public function addUser(/*Request $request, Response $response*/): Response
    {
        return new Response("Create user");

        $userRepository = $this->managerRegistry->getManager()->getRepository(User::class);
        $postData = $request->getParsedBody();

        $form_data = [
            'username' => Sanitizer::sanitize($postData['username']),
            'password' => $postData['password'],
            'confirmPassword' => $postData['confirmPassword'],
            'email' => Sanitizer::sanitize($postData['email'])
        ];

        $v = (new Validator($form_data))
            ->rule('required', ['username', 'password', 'confirmPassword', 'email'])
            ->rule('alphaNum', 'username')
            ->rule('lengthMin', 'password', 8)
            ->rule('email', 'email')->message('Invalid email')
            ->rule('equals', 'password', 'confirmPassword')->message('Both passwords must match')
            ->rule(function ($field, $value, $params, $fields) use ($userRepository) {
                if ($userRepository->findOneBy([$field => $value]) !== null) {
                    return false;
                }
                return true;
            }, 'username')->message('A user with the same username already exists');

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        } else {
            $em = $this->managerRegistry->getManager();

            $user = new User();
            $user->setUsername($form_data['username']);
            $user->setPassword($form_data['password']);
            $user->setEmail($form_data['email']);
            $em->persist($user);
            $em->flush();
        }

        $this->session->getFlash()->set('info', ['User successfully created']);
        $this->session->save();

        return $response
            ->withHeader('Location', $this->basePath . '/settings')
            ->withStatus(302);
    }
}

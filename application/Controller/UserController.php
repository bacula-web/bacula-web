<?php

declare(strict_types=1);

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

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(name="app_user_profile", path="/profile")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('pages/usersettings.html.twig' );
    }

    /**
     * @Route(name="app_user_create", path="/user/create")
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(): Response
    {
        $tplData = [];
        $postData = $request->getParsedBody();

        $this->username = $this->session->get('username');
        $user = $this->userTable->findByName($this->username);

        $tplData['username'] = $this->username;
        $tplData['email'] = $user->getEmail();

        return $this->view->render($response, 'pages/usersettings.html.twig', $tplData);
    }

    /**
     * @Route(path="/logout", name="app_logout")
     *
     */
    public function logout(): void {}

    /**
     * @Route(path="/user/resetpassword", name="app_user_resetpassword", methods={"POST"})
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return RedirectResponse
     */
    public function resetPassword(Request $request, UserPasswordHasherInterface $passwordHasher): RedirectResponse
    {
        $currentPassword = $request->request->get('oldpassword');

        if ($passwordHasher->isPasswordValid($this->getUser(), $currentPassword)) {
            $newPassword = $request->request->get('newpassword');
            $newPassword = $passwordHasher->hashPassword($this->getUser(), $newPassword);
            $this->entityManager->getRepository(User::class)->upgradePassword($this->getUser(),$newPassword);

            $this->addFlash('success', 'Password successfully updated');
        } else {
            $this->addFlash('danger', 'Provided current password is not valid');
        }

        return $this->redirectToRoute('app_user_profile');
    }
}

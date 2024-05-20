<?php

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of the Bacula-Web project.
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

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
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
        $form = $this->createForm(UserType::class, $this->getUser());

        return $this->render('pages/usersettings.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     *
     */
    public function logout(): void
    {
    }

    /**
     * @Route("/user/resetpassword", name="app_user_resetpassword", methods={"POST"})
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return RedirectResponse
     */
    public function resetPassword(Request $request, UserPasswordHasherInterface $passwordHasher): RedirectResponse
    {
        $currentPassword = $request->request->get('oldpassword');
        $user = $this->getUser();

        if ($passwordHasher->isPasswordValid($user, $currentPassword)) {
            $newPassword = $request->request->get('newpassword');

            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $newPassword
            ));

            $this->entityManager->flush();
            $this->entityManager->persist($user);

            $this->addFlash('success', 'Password successfully updated');
        } else {
            $this->addFlash('danger', 'Provided current password is not valid');
        }

        return $this->redirectToRoute('app_user_profile');
    }
}

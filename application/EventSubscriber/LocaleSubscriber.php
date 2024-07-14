<?php

/**
 * Copyright (C) 2004 Juan Luis Frances Jimenez
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

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var string|mixed
     */
    private string $defaultLocale;

    /**
     * @param ParameterBagInterface $parameter
     */
    public function __construct(ParameterBagInterface $parameter)
    {
        $this->defaultLocale = $parameter->get('app.language');
    }

    /**
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]]
        ];
    }
}

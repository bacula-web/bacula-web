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

namespace App\Controller;

use App\Service\AppCheck;
use Core\Exception\AppException;
use App\Service\Chart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="app_test")
     *
     * @param AppCheck $appChecks
     * @return Response
     * @throws AppException
     */
    public function index(AppCheck $appChecks): Response
    {
        $checksResults[] = $appChecks->checkGettextExtension();
        $checksResults[] = $appChecks->checkSessionExtension();
        $checksResults[] = $appChecks->checkSqliteExtension();
        $checksResults[] = $appChecks->checkMySqlExtension();
        $checksResults[] = $appChecks->checkPostgresExtension();
        $checksResults[] = $appChecks->checkPdoExtension();
        $checksResults[] = $appChecks->checkCacheDirIsWritable();
        $checksResults[] = $appChecks->checkPhpVersion();
        $checksResults[] = $appChecks->checkTimezone();

        $data = [
            'test' => 100,
            'test1' => 150,
            'test2' => 180,
            'test3' => 270,
            'test4' => 456
        ];

        $pieChart = new Chart([
            'type' => 'pie',
            'name' => 'chart_pie_test',
            'data' => $data
        ]);

        $barChart = new Chart([
            'type' => 'bar',
            'name' => 'chart_bar_test',
            'data' => $data,
            'ylabel' => 'Coffee cups'
        ]);

        return $this->render('pages/test.html.twig', [
            'app_checks' => $checksResults,
            'pie_chart_id' => $pieChart->getName(),
            'pie_chart' => $pieChart->render(),
            'bar_chart_id' => $barChart->getName(),
            'bar_chart' => $barChart->render(),
        ]);
    }
}

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

use App\Entity\Bacula\Repository\ClientRepository;
use App\Entity\Bacula\Repository\JobRepository;
use App\Form\ClientType;
use Core\Db\DatabaseFactory;
use Core\Exception\AppException;
use Core\Exception\ConfigFileException;
use Core\Exception\ValidationException;
use Core\Graph\Chart;
use Core\Db\CDBQuery;
use Core\Utils\DateTimeUtil;
use Core\Utils\CUtils;
use Core\Helpers\Sanitizer;
use App\Table\JobTable;
use App\Table\ClientTable;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClientController extends AbstractController
{
    /**
     * @param Request $request
     * @param JobRepository $jobRepository
     * @return Response
     * @throws AppException
     * @throws \DateInvalidOperationException
     * @throws \DateMalformedPeriodStringException
     */
    #[Route("/client", name: "app_client_report")]
    public function index(
        Request $request,
        JobRepository $jobRepository
    ): Response {

        $form = $this->createForm(ClientType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /**
             * TODO: get $start an $end from Bacula director database side
             */
            $daysInterval = $form->get('period')->getNormData();
            $end = new DateTimeImmutable('now');
            $interval = new DateInterval("P{$daysInterval}D");
            $start = $end->sub($interval);

            $client = $form->get('client')->getData();
            $jobs = $jobRepository->getClientJobs($client->getId(), $start, $end);

            $period = $form->get('period')->getData();

            // Get the last 7 days interval (start and end)
            $interval = new DateInterval('P1D');
            $datePeriod = new DatePeriod($start, $interval, $end);

            $daysStoredFiles = [];
            $daysStoredBytes = [];

            foreach($datePeriod as $day) {
                $daysStoredFiles[] = [
                    $day->format('m-d'),
                    $jobRepository->getTotalStoredFiles(
                        $day->setTime(0, 0, 0),
                        $day->setTime(23, 59, 59),
                        null,
                        $client->getId()
                    ),
                ];
            }

            $storedFilesChart = new Chart([
                'type' => 'bar',
                'name' => 'chart_storedfiles',
                'data' => $daysStoredFiles,
                'ylabel' => 'Files'
            ]);

            foreach($datePeriod as $day) {
                $daysStoredBytes[] = [
                    $day->format('m-d'),
                    $jobRepository->getTotalStoredBytes(
                        $day->setTime(0, 0, 0),
                        $day->setTime(23, 59, 59),
                        null,
                        $client->getId()
                    ),
                ];
            }

            $storedBytesChart = new Chart([
                'type' => 'bar',
                'name' => 'chart_storedbytes',
                'data' => $daysStoredBytes,
                'ylabel' => 'Bytes'
            ]);

            return $this->render('pages/client-report.html.twig', [
                'form' => $form,
                'client' => $client,
                'jobs' => $jobs,
                'total_bytes' => $jobRepository->getTotalStoredBytes($start, $end, null, $client->getId()),
                'total_files' => $jobRepository->getTotalStoredFiles($start, $end, null, $client->getId()),
                'period' => $period,
                'stored_bytes_chart_id' => $storedBytesChart->name,
                'stored_bytes_chart' => $storedBytesChart->render(),
                'stored_files_chart_id' => $storedFilesChart->name,
                'stored_files_chart' => $storedFilesChart->render(),
            ]);
        }

        return $this->render('pages/client-report.html.twig', [
            'form' => $form,
        ]);
    }
}

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

use App\Entity\Bacula\Repository\JobRepository;
use App\Form\BackupJobType;
use Core\Exception\AppException;
use Core\Graph\Chart;
use DateInterval;
use DateInvalidOperationException;
use DateMalformedPeriodStringException;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BackupJobController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private JobRepository $jobRepository;

    public function __construct(ManagerRegistry $doctrine, JobRepository $jobRepository)
    {
        $this->entityManager = $doctrine->getManager('bacula');
        $this->jobRepository = $jobRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws AppException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws DateInvalidOperationException
     * @throws DateMalformedPeriodStringException
     */
    #[Route("/backupjob", name: "backupjob")]
    public function index(Request $request): Response
    {
        $form = $this->createForm(BackupJobType::class);

        $form->handleRequest($request);

        $noReportOptions = 'true';
        $backupJobName = '';
        $daysInterval = 7;

        $jobs = [];
        $jobsList = [];

        $start = new DateTime('now');
        $end = new DateTime('now');

        $storedBytesChart = null;
        $storedFilesChart = null;

        if($form->isSubmitted() && $form->isValid()) {
            /**
             * TODO: use current datetime from database server
             */
            $daysInterval = $form->get('period')->getNormData();
            $end = new DateTimeImmutable('now');
            $interval = new DateInterval("P{$daysInterval}D");
            $start = $end->sub($interval);

            $noReportOptions = 'false';
            $backupJobName = $form->get('backupjob_name')->getNormData();
            $backupJobName = $backupJobName->getName();

            $jobQueryBuilder = $this->jobRepository->createQueryBuilder('j');
            $query = $jobQueryBuilder->select('j', 's')
                ->where("j.type = 'B'")
                ->andWhere('j.name = :jobname')
                ->andWhere('j.endtime BETWEEN :from AND :to')
                ->setParameters([
                    'from' => $start,
                    'to' => $end,
                    'jobname' => $backupJobName
                ])
                ->leftJoin('j.status', 's')
                ->orderBy('j.endtime', 'DESC')
                ->getQuery()
            ;
            $jobs = $query->getResult();

            // Get the last 7 days interval (start and end)
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($start, $interval, $end);

            // Last 7 days stored files chart
            $daysStoredFiles = [];
            $daysStoredBytes = [];

            foreach($period as $day) {
                $daysStoredFiles[] = [
                    $day->format('m-d'),
                    $this->jobRepository->getTotalStoredFiles(
                        $day->setTime(0, 0, 0),
                        $day->setTime(23, 59, 59),
                        $backupJobName
                    ),
                ];
            }

            $storedFilesChart = new Chart([
                'type' => 'bar',
                'name' => 'chart_storedfiles',
                'data' => $daysStoredFiles,
                'ylabel' => 'Files'
            ]);

            // Last 7 days stored bytes chart
            foreach($period as $day) {
                $daysStoredBytes[] = [
                    $day->format('m-d'),
                    $this->jobRepository->getTotalStoredBytes(
                        $day->setTime(0, 0, 0),
                        $day->setTime(23, 59, 59),
                        $backupJobName
                    ),
                ];
            }

            $storedBytesChart = new Chart([
                'type' => 'bar',
                'name' => 'chart_storedbytes',
                'data' => $daysStoredBytes,
                'uniformize_data' => true,
                'ylabel' => 'Bytes'
            ]);
        }

        return $this->render('pages/backupjob-report.html.twig', [
            'form' => $form,
            'jobs_list' => $jobsList,
            'jobs' => $jobs,
            'periods_list' => [],
            'selected_period' => $daysInterval,
            'stored_bytes_chart' => $storedBytesChart ? $storedBytesChart->render() : '',
            'stored_bytes_chart_id' => $storedBytesChart ? $storedBytesChart->name : '',
            'stored_files_chart' => $storedFilesChart ? $storedFilesChart->render() : '',
            'stored_files_chart_id' => $storedFilesChart ? $storedFilesChart->name : '',
            'no_report_options' => $noReportOptions,
            'backupjob_name' => $backupJobName,
            'perioddesc' => 'From ' . $start->format($this->getParameter('app.datetime_format_short')) . ' to ' . $end->format($this->getParameter('app.datetime_format_short')),
            'backupjobbytes' => $this->jobRepository->getTotalStoredBytes($start, $end, $backupJobName),
            'backupjobfiles' => $this->jobRepository->getTotalStoredFiles($start, $end, $backupJobName)
        ]);
    }
}

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

use App\Entity\Bacula\Repository\JobRepository;
use App\Entity\Bacula\Repository\VersionRepository;
use Core\Exception\AppException;
use App\Service\Chart;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackupJobController extends AbstractController
{
    /**
     * @var VersionRepository
     */
    private VersionRepository $catalog;

    /**
     * @param VersionRepository $catalog
     */
    public function __construct(VersionRepository $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @Route("/backupjob", name="backupjob", methods={"GET","POST"})
     *
     * @param Request $request
     * @param JobRepository $jobRepository
     * @return Response
     * @throws AppException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index(Request $request, JobRepository $jobRepository): Response
    {
        $periodsList = [
            [
                'days' => '7',
                'label' => 'Last week'
            ],
            [
                'days' => '14',
                'label' => 'Last 2 weeks'
            ],
            [
                'days' => '30',
                'label' => 'Last month'
            ]
        ];

        $backupJobName = $request->query->get('backupjob_name');
        $period = $request->query->get('backupjob_period', 7);

        /**
         * TODO: validate user input using validation
         */
        if ($request->getMethod() === 'POST') {
            $backupJobName = $request->request->get('backupjob_name');
            $period = $request->request->get('backupjob_period');
        }

        $backupJobsList = $jobRepository->getBackupJobsList();

        $to = $this->catalog->getCurrentDateTime();
        $from = $this->catalog->getCurrentDateTime()->subDays($period);

        $storedBytesChart = new Chart(
            [
                'type' => 'bar',
                'name' => 'chart_stored_bytes',
                'uniformize_data' => true,
                'data' => $jobRepository->getJobStoredBytes($from, $to, $backupJobName),
                'ylabel' => 'Bytes'
            ]
        );

        $storedFilesChart = new Chart(
            [
                'type' => 'bar',
                'name' => 'chart_stored_files',
                'uniformize_data' => true,
                'data' => $jobRepository->getJobStoredFiles($from, $to, $backupJobName),
                'ylabel' => 'Files'
            ]
        );

        $datetimeFormatShort = $this->getParameter('app.datetime_format_short');
        $periodDescription = 'From ' . $from->format($datetimeFormatShort) . ' to ' . $to->format($datetimeFormatShort);

        $jobQueryBuilder = $jobRepository->createQueryBuilder('j');
        $query = $jobQueryBuilder->select('j', 's')
            ->where("j.type = 'B'")
            ->andWhere('j.name = :jobname')
            ->andWhere('j.endtime BETWEEN :from AND :to')
            ->setParameters([
                    'from' => $from,
                    'to' => $to,
                    'jobname' => $backupJobName
                ])
            ->leftJoin('j.status', 's')
            ->orderBy('j.endtime', 'DESC')
            ->getQuery()
            ;

        $jobs = $query->getResult();

        return $this->render('pages/backupjob-report.html.twig', [
            'jobs_list' => $backupJobsList,
            'backupjob_name' => $backupJobName,
            'periods_list' => $periodsList,
            'backupjob_period' => $period,
            'jobs' => $jobs,
            'period_description' => $periodDescription,
            'backupjobbytes' => $jobRepository->getStoredBytesSum($from, $to, $backupJobName),
            'backupjobfiles' => $jobRepository->getStoredFilesSum($from, $to, $backupJobName),
            'stored_bytes_chart_id' => $storedBytesChart->getName(),
            'stored_bytes_chart' => $storedBytesChart->render(),
            'stored_files_chart_id' => $storedFilesChart->getName(),
            'stored_files_chart' => $storedFilesChart->render()
        ]);
    }
}

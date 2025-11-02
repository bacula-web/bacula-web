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
use App\Service\Chart\StoredFilesChart;
use App\Service\Chart\StoredBytesChart;
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

    private StoredFilesChart $storedFilesChart;
    private StoredBytesChart $storedBytesChart;

    /**
     * @param VersionRepository $catalog
     * @param StoredFilesChart $storedFilesChart
     * @param StoredBytesChart $storedBytesChart
     */
    public function __construct(
        VersionRepository $catalog,
        StoredFilesChart $storedFilesChart,
        StoredBytesChart $storedBytesChart
    ) {
        $this->catalog = $catalog;
        $this->storedFilesChart = $storedFilesChart;
        $this->storedBytesChart = $storedBytesChart;
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

        $jobName = $request->query->get('backupjob_name');
        $period = $request->query->get('backupjob_period', 7);

        /**
         * TODO: validate user input using validation
         */
        if ($request->getMethod() === 'POST') {
            $jobName = $request->request->get('backupjob_name');
            $period = $request->request->get('backupjob_period');
        }

        $backupJobsList = $jobRepository->getBackupJobsList();

        $to = $this->catalog->getCurrentDateTime();
        $from = $this->catalog->getCurrentDateTime()->subDays($period);

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
                    'jobname' => $jobName
                ])
            ->leftJoin('j.status', 's')
            ->orderBy('j.endtime', 'DESC')
            ->getQuery()
            ;

        $jobs = $query->getResult();

        $from = $this->catalog->getCurrentDateTime()->subDays($period);
        $to = $this->catalog->getCurrentDateTime();

        return $this->render('pages/backupjob-report.html.twig', [
            'jobs_list' => $backupJobsList,
            'backupjob_name' => $jobName,
            'periods_list' => $periodsList,
            'backupjob_period' => $period,
            'jobs' => $jobs,
            'period_description' => $periodDescription,
            'backupjobbytes' => $jobRepository->getStoredBytesSum($from, $to, $jobName),
            'backupjobfiles' => $jobRepository->getStoredFilesSum($from, $to, $jobName),
            'stored_bytes_chart' => $this->storedBytesChart->getChart($from, $to, null, $jobName),
            'stored_files_chart' => $this->storedFilesChart->getChart($from, $to, null, $jobName)
        ]);
    }
}

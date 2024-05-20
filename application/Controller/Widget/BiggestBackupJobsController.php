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

namespace App\Controller\Widget;

use App\Entity\Bacula\Repository\JobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class BiggestBackupJobsController extends AbstractController
{
    /**
     * @return Response
     */
    public function index(JobRepository $jobRepository): Response
    {
        $queryBuilder = $jobRepository->createQueryBuilder('j');

        $jobs = $queryBuilder
            ->select('j.name, j.jobfiles, j.jobbytes')
            ->where('j.status = :status')
            ->setParameter('status', 'T')
            ->andWhere('j.type = :type')
            ->setParameter('type', 'B')
            ->orderBy('j.jobbytes', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        /*
        $fields = ['Job.Jobbytes', 'Job.Jobfiles', 'Job.Name'];
        $where = ["Job.JobStatus = 'T'", "Job.Type = 'B'"];
        $res = [];

        $query = CDBQuery::get_Select(
            [
                'table' => $this->tablename,
                'fields' => $fields,
                'where' => $where,
                'orderby' => 'jobbytes DESC',
                'limit' => '10'
            ]
        );

        $result = $this->run_query($query);

        foreach ($result->fetchAll() as $job) {
            $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);
            $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);
            $res[] = $job;
        }

        return $res;
        */

        return $this->render('partials/widget/biggest_backup_jobs.html.twig', [
            'biggest_jobs' => $jobs
        ]);
    }
}

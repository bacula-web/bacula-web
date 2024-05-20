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

use App\Entity\Bacula\Job;
use App\Entity\Bacula\Repository\ClientRepository;
use App\Entity\Bacula\Repository\FilePriorV11Repository;
use App\Entity\Bacula\Repository\FileRepository;
use App\Entity\Bacula\Repository\JobRepository;
use App\Entity\Bacula\Repository\PoolRepository;
use App\Entity\Bacula\Repository\VersionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends AbstractController
{
    /**
     * @var JobRepository
     */
    private JobRepository $jobRepository;

    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameters;

    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * @var VersionRepository
     */
    private VersionRepository $catalog;

    /**
     * @var FileRepository
     */
    private FileRepository $fileRepository;

    /**
     * @var FilePriorV11Repository
     */
    private FilePriorV11Repository $filePriorV11Repository;

    /**
     * @var PoolRepository
     */
    private PoolRepository $poolRepository;

    /**
     * @param JobRepository $jobRepository
     * @param ClientRepository $clientRepository
     * @param ParameterBagInterface $parameters
     * @param PaginatorInterface $paginator
     * @param VersionRepository $catalog
     * @param FileRepository $fileRepository
     * @param FilePriorV11Repository $filePriorV11Repository
     * @param PoolRepository $poolRepository
     */
    public function __construct(
        JobRepository $jobRepository,
        ClientRepository $clientRepository,
        ParameterBagInterface $parameters,
        PaginatorInterface $paginator,
        VersionRepository $catalog,
        FileRepository $fileRepository,
        FilePriorV11Repository $filePriorV11Repository,
        PoolRepository $poolRepository
    ) {
        $this->jobRepository = $jobRepository;
        $this->clientRepository = $clientRepository;
        $this->parameters = $parameters;
        $this->paginator = $paginator;
        $this->catalog = $catalog;
        $this->fileRepository = $fileRepository;
        $this->filePriorV11Repository = $filePriorV11Repository;
        $this->poolRepository = $poolRepository;
    }

    /**
     * @Route("/jobs", name="jobs", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ParameterBagInterface $parameters
     * @return Response
     */
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        ParameterBagInterface $parameters
    ): Response {
        // Order result by
        $job_order = [
            'j.scheduledTime' => 'Job Scheduled Time',
            'j.starttime' => 'Job Start Date',
            'j.endtime'   => 'Job End Date',
            'j.id'     => 'Job Id',
            'j.name'  => 'Job Name',
            'j.jobbytes'  => 'Job Bytes',
            'j.jobfiles'  => 'Job Files',
            'p.name' => 'Pool Name'
        ];

        define('STATUS_ALL', 0);
        define('STATUS_RUNNING', 1);
        define('STATUS_WAITING', 2);
        define('STATUS_COMPLETED', 3);
        define('STATUS_COMPLETED_WITH_ERRORS', 4);
        define('STATUS_FAILED', 5);
        define('STATUS_CANCELED', 6);

        $job_status = [
            STATUS_RUNNING => 'Running',
            STATUS_WAITING => 'Waiting',
            STATUS_COMPLETED => 'Completed',
            STATUS_COMPLETED_WITH_ERRORS => 'Completed with errors',
            STATUS_FAILED => 'Failed',
            STATUS_CANCELED => 'Canceled'
        ];

        $jobTypesList = $this->jobRepository->getUsedJobTypes();
        $jobLevels = $this->jobRepository->getUsedLevels();

        /**
         * TODO: if hide_empty_pools is true, adapt code below
         */
        $poolsList = $this->poolRepository->findAll();
        $clientList = $this->clientRepository->getClients();

        $data = $this->jobRepository->findWithFilters($request);
        $jobQueryBuilder = $data['jobs'];

        $pagination = $paginator->paginate(
            $jobQueryBuilder,
            $request->query->getInt('page', 1)
        );

        $pagination->setItemNumberPerPage($parameters->get('app.rows_per_page'));

        return $this->render('pages/jobs.html.twig', [
                'pagination' => $pagination,
                'job_status' => $job_status,
                'job_types_list' => $jobTypesList,
                'levels_list' => $jobLevels,
                'clients_list' => $clientList,
                'pools_list' => $poolsList,
                'job_order' => $job_order,
                'filter_jobstatus' => $data['filter_jobstatus'],
                'filter_joblevel' => $data['filter_joblevel'],
                'filter_jobtype' => $data['filter_jobtype'],
                'filter_clientid' => $data['filter_clientid'],
                'filter_pool' => $data['filter_pool'],
                'filter_starttime' => $data['filter_starttime'],
                'filter_endtime' => $data['filter_endtime'],
                'filter_orderby' => $data['filter_orderby'],
                'filter_orderby_direction' => $data['filter_orderby_direction']
            ]);
    }

    /**
     * @Route("/joblog/{jobid}", name="joblog")
     *
     * @param int $jobid
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showLogs(int $jobid): Response
    {
        $qb = $this->jobRepository->createQueryBuilder('j');
        $qb
            ->select('j', 'l', 's', 'p')
            ->from(Job::class, 'j')
            ->leftJoin('j.logs', 'l')
            ->leftJoin('j.status', 's')
            ->leftJoin('j.pool', 'p')
            ->orderBy('l.time')
            ->where('j.id = :jobid')
            ->setParameter('jobid', $jobid);

        $job = $qb
            ->getQuery()
            ->getOneOrNullResult();

        if ($job) {
            return $this->render('pages/joblogs.html.twig', [
                'job' => $job
                ]);
        } else {
            throw $this->createNotFoundException("Job with id $jobid not found");
        }
    }

    /**
     * @Route("/jobfiles/{jobId}", name="jobfiles", methods={"GET"})
     *
     * @param int $jobId
     * @param Request $request
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function showFiles(
        int $jobId,
        Request $request
    ): Response {
        $filename = $request->query->get('filter_filename');

        $queryBuilder = $this->jobRepository->createQueryBuilder('j');

        $job = $queryBuilder
            ->select('j')
            ->leftJoin('j.status', 's')
            ->where('j.id = :jobid')
            ->setParameter('jobid', $jobId)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($job) {
            $job = $this->jobRepository->find($jobId);
        } else {
            $this->createNotFoundException("Job with id $jobId not found");
        }

        $catalogVersion = $this->catalog->getCatalogVersion();

        if ($catalogVersion < 1016) {
            $files = $this->filePriorV11Repository->findFilesByJobid($jobId, $filename);
        } else {
            $files = $this->fileRepository->findFilesByJobid($jobId, $filename);
        }

        $pagination = $this->paginator->paginate(
            $files['files'],
            $request->query->getInt('page', 1),
            $this->parameters->get('app.rows_per_page')
        );

        return $this->render('pages/jobfiles.html.twig', [
                'job' => $job,
                'filename' => $filename,
                'pagination' => $pagination
            ]);
    }
}

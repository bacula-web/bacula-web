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
use App\Entity\Bacula\JobSearch;
use App\Entity\Bacula\Repository\ClientRepository;
use App\Entity\Bacula\Repository\FilePriorV11Repository;
use App\Entity\Bacula\Repository\FileRepository;
use App\Entity\Bacula\Repository\JobRepository;
use App\Entity\Bacula\Repository\PoolRepository;
use App\Entity\Bacula\Repository\VersionRepository;
use App\Form\JobSearchType;
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
     * @param JobRepository $jobRepository
     * @return Response
     */
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        ParameterBagInterface $parameters,
        JobRepository $jobRepository
    ): Response {
        $jobSearch = new JobSearch($jobRepository);
        $form = $this->createForm(JobSearchType::class, $jobSearch);

        $form->handleRequest($request);

        $jobQueryBuilder = $jobRepository->createQueryBuilder('j');
        $jobQueryBuilder
            ->select('j', 's', 'p', 'c')
            ->leftJoin('j.pool', 'p')
            ->leftJoin('j.status', 's')
            ->leftJoin('j.client', 'c')
            ->orderBy($jobSearch->getOrderBy() ?? 'j.id', 'DESC');

        if ($form->isSubmitted() && $form->isValid()) {
            $jobQueryBuilder = $this->jobRepository->findWithFilters($jobQueryBuilder, $jobSearch);
        }

        $pagination = $paginator->paginate(
            $jobQueryBuilder,
            $request->query->getInt('page', 1)
        );
        $pagination->setItemNumberPerPage($parameters->get('app.rows_per_page'));

        return $this->render('pages/jobs.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination ]
        );
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

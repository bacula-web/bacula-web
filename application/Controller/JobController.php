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

use App\Entity\Bacula\Client;
use App\Entity\Bacula\File;
use App\Entity\Bacula\FileBeforeV11;
use App\Entity\Bacula\Job;
use App\Entity\Bacula\JobSearch;
use App\Entity\Bacula\Pool;
use App\Entity\Bacula\Repository\ClientRepository;
use App\Entity\Bacula\Repository\JobRepository;
use App\Entity\Bacula\Repository\PoolRepository;
use App\Entity\Bacula\Version;
use App\Form\JobFileSearchType;
use App\Form\JobSearchType;
use Carbon\Carbon;
use Core\Db\DBPagination;
use Core\Exception\ConfigFileException;
use Core\Exception\ValidationException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function Core\Helpers\getRequestParams;

class JobController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private ObjectManager $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager('bacula');
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */

    #[Route('/jobs', name: 'jobs')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $jobSearch = new JobSearch();

        $form = $this->createForm(JobSearchType::class, $jobSearch);
        $form->handleRequest($request);

        $jobsQueryBuilder = $this->entityManager->getRepository(Job::class)->createQueryBuilder('j');

        $jobsQueryBuilder
            ->select('j', 's', 'p', 'c')
            ->leftJoin('j.pool', 'p')
            ->leftJoin('j.status', 's')
            ->leftJoin('j.client', 'c')
        ;

        if ($jobSearch->getLevel()) {
            $jobsQueryBuilder
                ->andWhere('j.level = :level')
                ->setParameter('level', $jobSearch->getLevel());
        }

        if ($jobSearch->getType()) {
            $jobsQueryBuilder
                ->andWhere('j.type = :type')
                ->setParameter('type', $jobSearch->getType());
        }

        if ($jobSearch->getClient()) {
            $jobsQueryBuilder
                ->andWhere('j.client = :client')
                ->setParameter('client', $jobSearch->getClient());
        }

        if ($jobSearch->getPool()) {
            $jobsQueryBuilder
                ->andWhere('j.pool = :pool')
                ->setParameter('pool', $jobSearch->getPool());
        }

        if ($jobSearch->getStarttime()) {
            $jobsQueryBuilder
                ->andWhere('j.starttime >= :starttime')
                ->setParameter('starttime', $jobSearch->getStarttime());
        }

        if ($jobSearch->getEndtime()) {
            $jobsQueryBuilder
                ->andWhere('j.endtime <= :endtime')
                ->setParameter('endtime', $jobSearch->getEndtime());
        }

        if ($jobSearch->getStatus()) {
            switch ($jobSearch->getStatus()) {
                case 'running':
                    $jobsQueryBuilder
                        ->andWhere('j.status = :status')
                        ->setParameter('status', 'R');
                    break;
                case 'waiting':
                    $jobsQueryBuilder
                        ->andWhere('j.status IN(:status)')
                        ->setParameter('status', ['F', 'S', 'M', 'm', 's', 'j', 'c', 'd', 't', 'p', 'C']);
                    break;
                case 'completed':
                    $jobsQueryBuilder
                        ->andWhere('j.status IN(:status)')
                        ->setParameter('status', 'T');
                    break;
                case 'completed-with-errors':
                    $jobsQueryBuilder
                        ->andWhere('j.status IN(:status)')
                        ->setParameter('status', 'E');
                    break;
                case 'failed':
                    $jobsQueryBuilder
                        ->andWhere('j.status IN(:status)')
                        ->setParameter('status', 'f');
                    break;
                case 'cancelled':
                    $jobsQueryBuilder
                        ->andWhere('j.status IN(:status)')
                        ->setParameter('status', 'A');
                    break;
            }
        }

        $jobsQueryBuilder->orderBy(
            $jobSearch->getOrderby() ?? 'j.id',
            $jobSearch->getOrderDirection() ? 'ASC' : 'DESC'
        );

        return $this->render('pages/jobs.html.twig', [
            'form' => $form,
            'pagination' => $paginator
                ->paginate(
                    $jobsQueryBuilder->getQuery(),
                    $request->query->getInt('page', 1),
                    $this->getParameter('app.rows_per_page')
                )
        ]);
    }

    /**
     * @param Job|null $job
     * @return Response
     */
    #[Route("/joblog/{id}", name: "joblog")]
    public function showLogs(?Job $job): Response
    {
        if (null === $job) {
            $this->addFlash('error', 'Invalid job id provided in Job logs report');
            return $this->redirectToRoute('jobs');
        }

        $job = $this->entityManager->getRepository(Job::class)->getJobWithLogs($job->getId());

        return $this->render('pages/joblogs.html.twig', [
            'job' => $job
        ]);
    }

    /**
     * @param Request $request
     * @param int $id Job id
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route("/job/{id}/files", name: "jobfiles")]
    public function showFiles(Request $request, int $id, PaginatorInterface $paginator): Response
    {
        $filename = '';

        $form = $this->createForm(JobFileSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filename = $form->get('filename')->getData();
        }

        // Get job details
        $job = $this->entityManager->createQueryBuilder()
            ->select('j, s')
            ->from(Job::class, 'j')
            ->join('j.status', 's')
            ->where('j.id = :jobId')
            ->setParameter('jobId', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$job) {
            $this->createNotFoundException('Job with provided id not found');
        }

        $version = $this->entityManager->getRepository(Version::class)->getCatalogVersion();

        if ($version < 1016) {
            $repository = $this->entityManager->getRepository(FileBeforeV11::class);
        } else {
            $repository = $this->entityManager->getRepository(File::class);
        }

        /**
         * @var QueryBuilder $filesQueryBuilder
         */
        $filesQueryBuilder = $repository->getFilesFromJobId($job->getId(), $filename);

        $pagination = $paginator->paginate(
            $filesQueryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            $this->getParameter('app.rows_per_page')
        );

        dump($pagination->getItems());

        return $this->render('pages/jobfiles.html.twig', [
            'job' => $job,
            'pagination' => $pagination,
            'form' => $form,
        ]);
    }
}

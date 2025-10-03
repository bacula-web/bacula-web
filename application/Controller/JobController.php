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
use App\Entity\Bacula\Pool;
use App\Entity\Bacula\Repository\ClientRepository;
use App\Entity\Bacula\Repository\JobRepository;
use App\Entity\Bacula\Repository\PoolRepository;
use App\Entity\Bacula\Version;
use Core\Controller\AbstractController;
use Core\Db\DBPagination;
use Core\Exception\ConfigFileException;
use Core\Exception\ValidationException;
use Core\Helpers\Sanitizer;
use Doctrine\ORM\QueryBuilder;
use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

use function Core\Helpers\getRequestParams;

class JobController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     * @throws ConfigFileException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function index(Request $request, Response $response): ResponseInterface
    {
        $em = $this->managerRegistry->getManager('bacula');

        /**
         * @var QueryBuilder $jobsQueryBuilder
         */
        $jobsQueryBuilder = $em->createQueryBuilder();

        $jobsQueryBuilder
            ->select('j, p, s')
            ->from(Job::class, 'j')
            ->leftJoin('j.pool', 'p')
            ->leftJoin('j.status', 's');

        $postRequestData = getRequestParams($request);

        // Job status

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

        // Job level
        /**
         * @var JobRepository $jobRepository
         */
        $jobRepository = $em->getRepository(Job::class);
        $levels_list = $jobRepository->getUsedLevels();

        // Job Type
        $jobTypesList = $jobRepository->getUsedJobTypes();

        // Job Client
        /**
         * @var ClientRepository $clientRepository
         */
        $clientRepository = $em->getRepository(Client::class);
        $clientsList = $clientRepository->getClients($this->config->get('show_inactive_clients'));

        // Job pool
        /**
         * @var PoolRepository $poolRepository
         */
        $poolRepository = $em->getRepository(Pool::class);
        $poolsList = $poolRepository->getPoolsList($this->config->get('hide_empty_pools'));

        // Order by
        $result_order = [
            'j.scheduledTime' => 'Job Scheduled Time',
            'j.starttime' => 'Job Start Date',
            'j.endtime'   => 'Job End Date',
            'j.id'     => 'Job Id',
            'j.name'  => 'Job Name',
            'j.jobbytes'  => 'Job Bytes',
            'j.jobfiles'  => 'Job Files',
            'j.pool.Name' => 'Pool Name'
        ];

        if (!empty($postRequestData)) {
            // Validate user input
            $validator = new Validator($postRequestData, [
                'filter_jobstatus',
                'filter_joblevel',
                'filter_jobtype',
                'filter_clientid',
                'filter_poolid',
                'filter_job_starttime',
                'filter_job_endtime',
                'filter_job_orderby',
                'filter_job_orderby_asc',
                'page'
            ]);

            $validator
                ->rule('in', 'filter_jobtype', array_keys($jobTypesList))->message('Invalid job type')
                ->rule('in', 'filter_joblevel', array_keys($levels_list))->message('Invalid job level')
                ->rule('in', 'filter_jobstatus', array_keys($job_status))->message('Invalid job status')
                ->rule('in', 'filter_clientid', array_keys($clientsList))->message('Invalid client')
                ->rule('in', 'filter_poolid', array_keys($poolsList))->message('Invalid pool')
                ->rule('date', 'filter_job_starttime')->message('Invalid job start time')
                ->rule('date', 'filter_job_endtime')->message('Invalid job end time')
                ->rule('in', 'filter_job_orderby', $result_order)->message('Invalid job order by')
                ->rule('in', 'filter_job_orderby_asc', ['ASC', 'DESC'])->message('Invalid job order by')
                ->rule('integer', 'page')->message('Invalid page number');

            if (!$validator->validate()) {
                throw new ValidationException($validator->errors());
            }
        }

        /**
         * POST VALIDATION
         */

        // Job status

        $filter_jobstatus = (isset($postRequestData['filter_jobstatus'])) ? (int) $postRequestData['filter_jobstatus'] : null;

        switch ($filter_jobstatus) {
            case STATUS_RUNNING:
                $jobsQueryBuilder
                    ->andWhere('j.status = :status')
                    ->setParameter('status', 'R');
                break;
            case STATUS_WAITING:
                $jobsQueryBuilder
                    ->andWhere('j.status IN (:status)')
                    ->setParameter('status', ['F','S','M','m','s','j','c','d','t','p','C']);
                break;
            case STATUS_COMPLETED:
                $jobsQueryBuilder
                    ->andWhere('j.status = :status')
                    ->setParameter('status', 'T');
                break;
            case STATUS_COMPLETED_WITH_ERRORS:
                $jobsQueryBuilder
                    ->andWhere('j.status = :status')
                    ->setParameter('status', 'E');
                break;
            case STATUS_FAILED:
                $jobsQueryBuilder
                    ->andWhere('j.status = :status')
                    ->setParameter('status', 'f');
                break;
            case STATUS_CANCELED:
                $jobsQueryBuilder
                    ->andWhere('j.status = :status')
                    ->setParameter('status', 'A');
                break;
        }

        // Job level filter

        $filterJobLevel = (isset($postRequestData['filter_joblevel'])) ? $postRequestData['filter_joblevel'] : null;

        if ($filterJobLevel) {
            $jobsQueryBuilder
                ->andWhere('j.level = :job_level')
                ->setParameter('job_level', $filterJobLevel);
        }

        // Job Type

        $filterJobType = $postRequestData['filter_jobtype'] ?? null;

        if ($filterJobType) {
            $jobsQueryBuilder
                ->andWhere('j.type = :job_type')
                ->setParameter('job_type', $filterJobType);
        }

        // Job Client
        $filterClientId = $postRequestData['filter_clientid'] ?? null;
        if ($filterClientId) {
            $jobsQueryBuilder
                ->andWhere('j.clientid = :clientid')
                ->setParameter('clientid', $filterClientId);
        }

        // Job Pool
        $filterPoolId = (isset($postRequestData['filter_poolid'])) ? $postRequestData['filter_poolid'] : null;

        if ($filterPoolId) {
            $jobsQueryBuilder
                ->andWhere('j.poolid = :pool_id')
                ->setParameter('pool_id', $filterPoolId);
        }

        // Job start time

        $filterJobStartTime = (isset($postRequestData['filter_job_starttime'])) ? $postRequestData['filter_job_starttime'] : null;

        if ($filterJobStartTime) {
            $jobsQueryBuilder
                ->andWhere('j.starttime >= :start_time')
                ->setParameter('start_time', $filterJobStartTime);
        }

        // Job end time

        $filterJobEndTime = (isset($postRequestData['filter_job_endtime'])) ? $postRequestData['filter_job_endtime'] : null;

        if ($filterJobEndTime) {
            $jobsQueryBuilder
                ->andWhere('j.endtime >= :end_time')
                ->setParameter('end_time', $filterJobEndTime);
        }

        // Order by
        $jobOrderByFilter = 'j.id';
        if (isset($postRequestData['filter_job_orderby'])) {
            $jobOrderByFilter = $postRequestData['filter_job_orderby'];
        }

        // Order direction

        $job_orderby_asc_filter = (isset($postRequestData['filter_job_orderby_asc'])) ? 'ASC' : 'DESC';
        $jobOrderDirectionChecked = ($job_orderby_asc_filter == 'ASC') ? 'checked' : '';

        $jobsQueryBuilder->orderBy($jobOrderByFilter, $job_orderby_asc_filter);

        $pagination = new DBPagination($request, $this->config);

        $last_jobs = $pagination->paginate($jobsQueryBuilder, $jobRepository->count([]));

        return $this->view->render($response, 'pages/jobs.html.twig', [
            'pagination' => $pagination,
            'last_jobs' => $last_jobs,
            'job_status' => $job_status,
            'levels_list' => $levels_list,
            'job_types_list' => $jobTypesList,
            'clients_list' => $clientsList,
            'pools_list' => $poolsList,
            'filter_clientid' => $filterClientId,
            'filter_joblevel' => $filterJobLevel,
            'filter_jobstatus' => $filter_jobstatus,
            'filter_job_type' => $filterJobType,
            'filter_pool_id' => $filterPoolId,
            'filter_job_starttime' => $filterJobStartTime,
            'filter_job_endtime' => $filterJobEndTime,
            'result_order' => $result_order,
            'result_order_field' => $jobOrderByFilter,
            'result_order_asc_checked' => $jobOrderDirectionChecked,
        ]);
    }

    /**
     * @param Response $response
     * @param int $id Job Id
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showLogs(Response $response, int $id): ResponseInterface
    {
        $repository = $this
            ->managerRegistry->getManager('bacula')
            ->getRepository(Job::class);

        $v = new Validator([$id]);
        $v->rules(['integer' => 'jobid']);

        if (!$v->validate()) {
            $this->session->getFlash()->set('error', ['Invalid job id provided in Job logs report']);
            return $response
                ->withHeader('Location', $this->basePath . '/jobs')
                ->withStatus(302);
        }

        $job = $repository->getJobWithLogs($id);

        /**
         * TODO: return 404 if $job is null
         */
        return $this->view->render($response, 'pages/joblogs.html.twig', [
            'job' => $job
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $id Job id
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showFiles(Request $request, Response $response, int $id): ResponseInterface
    {
        $filename = '';

        if ($request->getMethod() === 'POST') {
            $postData = $request->getParsedBody();

            $validator = (new Validator($postData))
                ->rule('alphanum', 'filename')->message('Filename/path must be alphanumeric only');

            if (! $validator->validate()) {
                throw new ValidationException($validator->errors());
            }
        }

        /**
         * TODO: throw 404 if jobid does not exist
         * TODO: below code is not necessary as $id is always provided
         * TODO: make sure jobid is a valid positive integer
         */

        if (isset($postData['filename'])) {
            $filename = Sanitizer::sanitize($postData['filename']);
        }

        $em = $this->managerRegistry->getManager('bacula');

        $job = $em->createQueryBuilder()
            ->select('j, s')
            ->from(Job::class, 'j')
            ->join('j.status', 's')
            ->where('j.id = :jobId')
            ->setParameter('jobId', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $version = $em->getRepository(Version::class)->getCatalogVersion();

        if ($version < 1016) {
            $repository = $em->getRepository(FileBeforeV11::class);
        } else {
            $repository = $em->getRepository(File::class);
        }
        $filesQueryBuilder = $repository->getFilesFromJobId($job->getId(), $filename);
        $totalFiles = $repository->count(['jobid' => $id]);

        $paginator = new DBPagination($request, $this->config);

        return $this->view->render($response, 'pages/jobfiles.html.twig', [
            'job' => $job,
            'job_files_count' => 'to fix',
            'job_files' => $paginator->paginate($filesQueryBuilder, $totalFiles),
            'pagination' => $paginator,
            'total_files' => $totalFiles,
            'filename' => $filename
        ]);
    }
}

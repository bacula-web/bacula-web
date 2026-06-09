<?php

/**
 * Copyright (C) 2004 Juan Luis Frances Jimenez
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

namespace App\Controller;

use App\Entity\Bacula\Job;
use App\Entity\Bacula\Volume;
use App\Service\Chart\StatsChart;
use Core\Exception\ConfigFileException;
use Core\Exception\AppException;
use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
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
     * @throws AppException
     * @throws ConfigFileException
     */
    #[Route('/', name: 'home')]
    public function index(Request $request): Response {
        $tplData = [];

        //$routeContext = RouteContext::fromRequest($request);
        //$routeParser = $routeContext->getRouteParser();
        $jobsPageUrl = '/jobs';
        $poolsPageUrl = '/pools';

        //$selectedPeriod = 'last_day';
        //$postData = $request->getParsedBody();
        //if (isset($postData['period_selector'])) {
        //    $selectedPeriod = Sanitizer::sanitize($postData['period_selector']);
        //}

        //$tplData['custom_period_list_selected'] = $selectedPeriod;

        /*
        $tplData['custom_period_list'] = [
            ['id' => 'last_day', 'label' => 'Last 24 hours'],
            ['id' => 'last_week', 'label' => 'Last 7 days'],
            ['id' => 'last_month', 'label' => 'Last 4 weeks (28 days)'],
            ['id' => 'since_bot', 'label' => 'Since BOT']
        ];


        if ($request->getMethod() === 'POST') {
            $validator = new Validator($postData, ['period_selector']);

            $validator
                ->rule('in', 'period_selector', ['last_day', 'last_week', 'last_month', 'since_bot'])
                ->message('Invalid period');

            if (!$validator->validate()) {
                throw new ValidationException($validator->errors());
            }
        }
        */
        $jobRepository = $this->entityManager->getRepository(Job::class);
        $volumeRepository = $this->entityManager->getRepository(Volume::class);

        /**
         * @var $customPeriod array<DateTime,DateTime>
         */
        $customPeriod[0] = new DateTime('24 hours ago');
        // TODO: get timestamp from DB server
        $customPeriod[1] = new DateTimeImmutable('now');

        $form = $this->createFormBuilder()
            ->add('period', ChoiceType::class, [
                'choices' => [
                    'Last 24 hours' => 1,
                    'Last 7 days' => 7,
                    'Last 4 weeks (28 days)' => 28,
                    'Since BOT' => 0
                ],
                'attr' => [
                    'class' => 'form-control input-sm'
                ],
                'label_attr' => [
                    'class' => 'input-sm'
                ]
            ])
            ->add('submit', SubmitType::class)
            ->setMethod('GET')
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $daysInterval = $form->get('period')->getData();

            if($daysInterval === 0) {
                $customPeriod[0] = new DateTime('1970-01-01');
            } else {
                $interval = new \DateInterval("P{$daysInterval}D");
                $customPeriod[0] = $customPeriod[1]->sub($interval);
            }
        }

        $dateFormat = 'l, F j o';
        $literalPeriod = date_format($customPeriod[0], $dateFormat) . ' to ' . date_format($customPeriod[1], $dateFormat);

        $statsChart = new StatsChart($this->entityManager);
        $lastJobsChart = $statsChart->getLastJobsChart($customPeriod[0],$customPeriod[1], $this->generateUrl('jobs'));
        $poolsUsageChart = $statsChart->getPoolsUsageChart($this->generateUrl('pools'));
        $storedBytesChart = $statsChart->getStoredBytesChart();
        $storedFilesChart = $statsChart->getStoredFilesChart();

        return $this->render('pages/dashboard.html.twig', [
           'form' => $form,
            'literal_period' => $literalPeriod,
            'running_jobs' => $jobRepository->countJobsByStatus('running'),
            'completed_jobs' => $jobRepository->countJobsByStatus('completed', $customPeriod[0], $customPeriod[1]),
            'completed_jobs_with_errors' => $jobRepository->countJobsByStatus('completed_with_errors', $customPeriod[0], $customPeriod[1]),
            'failed_jobs' => $jobRepository->countJobsByStatus('failed', $customPeriod[0], $customPeriod[1]),
            'waiting_jobs' => $jobRepository->countJobsByStatus('waiting'),
            'canceled_jobs' => $jobRepository->countJobsByStatus('canceled', $customPeriod[0], $customPeriod[1]),
            'bytes_last' => $jobRepository->getTotalStoredBytes($customPeriod[0], $customPeriod[1]),
            'files_last' => $jobRepository->getTotalStoredFiles($customPeriod[0], $customPeriod[1]),
            'incr_jobs' => $jobRepository->countJobsByLevel($customPeriod[0], $customPeriod[1], 'I'),
            'diff_jobs' => $jobRepository->countJobsByLevel($customPeriod[0], $customPeriod[1], 'D'),
            'full_jobs' => $jobRepository->countJobsByLevel($customPeriod[0], $customPeriod[1], 'F'),
            'last_jobs_chart_id' => $lastJobsChart->name,
            'last_jobs_chart' => $lastJobsChart->render(),
            'pools_usage_chart_id' => $poolsUsageChart->name,
            'pools_usage_chart' => $poolsUsageChart->render(),
            'storedbytes_chart_id' => $storedBytesChart->name,
            'storedbytes_chart' => $storedBytesChart->render(),
            'storedfiles_chart_id' => $storedFilesChart->name,
            'storedfiles_chart' => $storedFilesChart->render(),
            'volumes_list' => $volumeRepository->getLastUsedVolumes(),
            'jobnames_jobs_stats' => $jobRepository->getJobNameStats(),
            'job_types_jobs_stats' => $jobRepository->getStatisticsPerType(),
            'weekly_jobs_stats' => $jobRepository->getWeeklyJobsStats(),
            'biggestjobs' => $jobRepository->getBiggestJobs()
        ]);
    }
}

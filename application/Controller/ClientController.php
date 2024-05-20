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

use App\Entity\Bacula\Repository\ClientRepository;
use App\Entity\Bacula\Repository\JobRepository;
use App\Entity\Bacula\Repository\VersionRepository;
use App\Form\ClientType;
use Core\Exception\AppException;
use App\Service\Chart;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class ClientController extends AbstractController
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameter;

    /**
     * @var JobRepository
     */
    private JobRepository $jobRepository;

    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    /**
     * @var VersionRepository
     */
    private VersionRepository $catalog;

    /**
     * @param VersionRepository $catalog
     * @param ClientRepository $clientRepository
     * @param JobRepository $jobRepository
     * @param ParameterBagInterface $parameter
     */
    public function __construct(
        VersionRepository $catalog,
        ClientRepository $clientRepository,
        JobRepository $jobRepository,
        ParameterBagInterface $parameter
    ) {
        $this->clientRepository = $clientRepository;
        $this->jobRepository = $jobRepository;
        $this->parameter = $parameter;
        $this->catalog = $catalog;
    }

    /**
     * @Route("/client/{clientId?}", name="clients", methods={"GET"})
     *
     * @param Request $request
     * @param int|null $clientId
     * @return Response
     * @throws AppException
     * @throws Exception
     */
    public function index(Request $request, ?int $clientId): Response
    {
        $form = $this->createForm(ClientType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $client = $formData['client'];

            $client = $this->clientRepository->find($client->getId());

            if (!$client) {
                throw $this->createNotFoundException("Client with id $clientId not found");
            }

            $to = $this->catalog->getCurrentDateTime();
            $from = $this->catalog->getCurrentDateTime()->subDays($formData['period']);

            $storedBytesChart = new Chart(
                [
                    'type' => 'bar',
                    'name' => 'chart_storedbytes',
                    'uniformize_data' => true,
                    'data' => $this->jobRepository->getJobStoredBytes($from, $to, null, $clientId),
                    'ylabel' => 'Bytes'
                ]
            );

            $storedFilesChart = new Chart(
                [
                    'type' => 'bar',
                    'name' => 'chart_storedfiles',
                    'uniformize_data' => true,
                    'data' => $this->jobRepository->getJobStoredFiles($from, $to, null, $clientId),
                    'ylabel' => 'Files'
                ]
            );

            $backupJobs = $this->jobRepository->getClientJobs($client->getId(), $from, $to);

            return $this->render('pages/client-report.html.twig', [
                'form' => $form->createView(),
                'client' => $client,
                'period' => $form->get('period')->getData(),
                'backup_jobs' => $backupJobs,
                'stored_bytes_chart_id' => $storedBytesChart->getName(),
                'stored_bytes_chart' => $storedBytesChart->render(),
                'stored_files_chart_id' => $storedFilesChart->getName(),
                'stored_files_chart' => $storedFilesChart->render()
            ]);
        }

        return $this->render('pages/client-report.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

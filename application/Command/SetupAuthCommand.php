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

namespace App\Command;

use App\Entity\Core\User;
use App\Table\UserTable;
use Core\Db\DatabaseFactory;
use Core\Db\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class SetupAuthCommand extends Command
{
    /**
     * @var ObjectManager
     */
    private ObjectManager $em;

    public function __construct(string $name = null, ManagerRegistry $managerRegistry)
    {
        parent::__construct($name);

        $this->em = $managerRegistry->getManager();
    }

    protected function configure(): void
    {
        $this->setDescription('Setup Bacula-Web users authentication database')
            ->setHelp('This command setup users authentication database')
            ->setName('setupauth');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('It\'s now time to setup the application back-end database');

        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $importantInfo = $formatter->formatSection(
            'Important!',
            'Please note that all information stored in the user database will be destroyed'
        );
        $output->writeln($importantInfo);

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion('Can we proceed ?', ['yes', 'no'], 1);
        $question->setErrorMessage('Answer %s is not valid.');

        $answer = $helper->ask($input, $output, $question);
        $output->writeln('You have selected: ' . $answer);

        if ($answer !== 'yes') {
            $errorMessages = ['Aborted', 'Auth database creation canceled by user, exiting.'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
            $output->writeln($formattedBlock);

            return Command::INVALID;
        }

        $output->writeln('Deleting users authentication database');

        if (file_exists('application/assets/protected/application.db')) {
            if (unlink('application/assets/protected/application.db')) {
                $output->writeln('<info>Database file removed</info>');
            } else {
                $output->writeln('<error>Fail to remove database file !!!</error>');
                return Command::FAILURE;
            }
        }

        try {
            $output->writeln('Creating database schema');

            $userTable = new UserTable(
                DatabaseFactory::getDatabase()
            );

            if ($userTable->createSchema() === 0) {
                $output->writeln('<info>Database created</info>');
            } else {
                $output->writeln('<error>Database schema not created</error>');
                return Command::FAILURE;
            }

            $output->writeln('User creation');

            $question = new Question('Username: ', 'admin');
            $username = $helper->ask($input, $output, $question);

            $question = new Question('Email: ', 'admin@local.net');
            $email = $helper->ask($input, $output, $question);

            $question = new Question('Password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);

            $password = $helper->ask($input, $output, $question);

            if (strlen($password) < 8) {
                $output->writeln('<error>Password must be at least 8 characters long, aborting</error>');
                return Command::FAILURE;
            }

            $user = new  User();
            $user->setUsername($username);
            $user->setPassword($password);
            $user->setEmail($email);

            $this->em->persist($user);
            $this->em->flush();

            $output->writeln('<info> User created successfully</info>');

            $output->writeln('You can now connect to your Bacula-Web instance using provided credentials');
        } catch (Exception $e) {
            $output->writeln(
                '<error>Error</error> Unable to create user with error ' . $e->getMessage() .
                ' error code(' . $e->getCode() . ')'
            );
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

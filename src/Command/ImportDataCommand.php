<?php

namespace App\Command;

use App\Service\ImportDataService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDataCommand extends Command
{
    private $importService;

    public function __construct(ImportDataService $importService)
    {
        parent::__construct();

        $this->importService = $importService;
    }

    protected function configure()
    {
        $this
            ->setName('app:import-data')
            ->setDescription('Imports external data to database.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importService->insertDataIntoDatabase($output);

        $output->writeln('<info>====> Data has been successfully inserted into database. <====</info>');

        return $this::SUCCESS;
    }
}

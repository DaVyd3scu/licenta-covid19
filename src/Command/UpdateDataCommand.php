<?php

namespace App\Command;

use App\Service\UpdateDataService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDataCommand extends Command
{
    private $updateService;

    public function __construct(UpdateDataService $updateService)
    {
        parent::__construct('app:update-data');

        $this->updateService = $updateService;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>===========> Updating data <===========</info>');

        $this->updateService->updateData($output);

        return $this::SUCCESS;
    }
}

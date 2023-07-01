<?php

namespace ItsTreason\AptRepo\Command\Debug;

use ItsTreason\AptRepo\Repository\SuitesRepository;
use ItsTreason\AptRepo\Service\PackageListService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegeneratePackageLists extends Command
{
    public const NAME = 'debug:regenerate-package-lists';

    public function __construct(
        private readonly PackageListService $packagelistService,
        private readonly SuitesRepository $suitesRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Regenerates package lists for all suites');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $suites = $this->suitesRepository->getAll();

        foreach ($suites as $suite) {
            $this->packagelistService->updatePackageLists($suite);
        }

        return self::SUCCESS;
    }
}

<?php

namespace ItsTreason\AptRepo\Command\Cron;

use ItsTreason\AptRepo\Repository\GitHubSubscriptionRepository;
use ItsTreason\AptRepo\Service\GitHubApiService;
use ItsTreason\AptRepo\Service\PackageParseService;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateGitHubSubsctionsCommand extends Command
{
    public const NAME = 'cron:update-github-subscriptions';

    public function __construct(
        private readonly GitHubApiService $githubApi,
        private readonly GitHubSubscriptionRepository $subscriptionRepository,
        private readonly PackageParseService $packageParser,
        private readonly Logger $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Tries to fetch new releases from GitHub subscriptions and upload new packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $subscriptions = $this->subscriptionRepository->getAll();

        foreach ($subscriptions as $subscription) {
            $releases = $this->githubApi->getNewReleases($subscription);

            $lastRelease = null;
            foreach ($releases as $release) {
                $this->logger->info('Found new github release', [
                    'repository' => sprintf('%s/%s', $subscription->getOwner(), $subscription->getName()),
                    'newVersion' => $release->getReleaseName(),
                    'fileFiles' => $release->getFiles(),
                ]);

                $lastRelease = $lastRelease ?? $release->getReleaseName();
                $this->packageParser->addPackagesFromRelease($release);
            }

            if (!$lastRelease) {
                continue;
            }

            $subscription = $subscription->withNewLastRelease($lastRelease);
            $this->subscriptionRepository->insert($subscription);
        }

        return self::SUCCESS;
    }
}

<?php

namespace ItsTreason\AptRepo\Service;

use GuzzleHttp\Client;
use ItsTreason\AptRepo\Value\GitHubRelease;
use ItsTreason\AptRepo\Value\GitHubSubscription;

class GitHubApiService {
    private const RELEASE_URL = 'https://api.github.com/repos/%s/%s/releases';

    public function __construct(
        private readonly Client $httpClient,
    ) {}

    /**
    * @return GitHubRelease[]
    */
    public function getNewReleases(GitHubSubscription $subscription): array
    {
        // Example: https://api.github.com/repos/obsidianmd/obsidian-releases/releases
        $url = sprintf(self::RELEASE_URL, $subscription->getOwner(), $subscription->getName());
        $response = $this->httpClient->get($url);
        $releaseData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        $releases = [];
        foreach ($releaseData as $release) {
            $name = $release['name'];
            // The Api return all releases from latest to oldest
            if ($subscription->getLastRelease() === $name) {
                break;
            }

            $files = [];
            foreach ($release['assets'] as $asset) {
                if (!str_ends_with($asset['name'], '.deb')) {
                    continue;
                }
                $files[] = $asset['browser_download_url'];
            }

            if (count($files) > 0) {
                $releases[] = GitHubRelease::create($name, $files);
            }
        }

        return $releases;
    }
}

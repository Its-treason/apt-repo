<?php

namespace ItsTreason\AptRepo\Api\Ui\GitHubSubscription;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use ItsTreason\AptRepo\Repository\GitHubSubscriptionRepository;
use ItsTreason\AptRepo\Repository\PackageMetadataRepository;
use ItsTreason\AptRepo\Service\GitHubApiService;
use ItsTreason\AptRepo\Value\GitHubSubscription;
use Psr\Http\Message\ResponseInterface;
use ItsTreason\AptRepo\Service\PackageParseService;
use Psr\Http\Message\ServerRequestInterface;

class GitHubSubscriptionCreateController
{
    public function __construct(
        private readonly GitHubApiService $githubApi,
        private readonly PackageMetadataRepository $packageMetadataRepository,
        private readonly GitHubSubscriptionRepository $subscriptionRepository,
        private readonly PackageParseService $packageParser,
        private readonly Client $httpClient,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getParsedBody();
        if (!isset($params['url'])) {
            return $response->withStatus(302)
                ->withHeader('Location', '/ui/subscription?error=Missing values');
        }
        $url = $params['url'];
        if (!preg_match('/https:\/\/github.com\/[\w\-_]{1,}\/[\w\-_]{1,}/', $url)) {
            return $response->withStatus(302)
                ->withHeader('Location', '/ui/subscription?error=Invalid Url');
        }

        [$name, $owner] = array_reverse(explode('/', $url));
        $subscription = GitHubSubscription::create($owner, $name, null);

        try {
            $releases = $this->githubApi->getNewReleases($subscription);
        } catch (Exception $exception) {
            $message = urlencode('Failed to fetch Release: ' . $exception->getMessage());
            return $response->withStatus(302)
                ->withHeader('Location', '/ui/subscription?error=' . $message);
        }

        $lastRelease = null;
        foreach ($releases as $release) {
            $lastRelease = $release->getReleaseName();
            foreach ($release->getFiles() as $downloadUrl) {
                try {
                    $file = sprintf('/tmp/%s.deb', bin2hex(random_bytes(12)));
                    $this->httpClient->get($downloadUrl, [
                        RequestOptions::SINK => $file,
                        RequestOptions::ALLOW_REDIRECTS => true,
                    ]);

                    $metadata = $this->packageParser->collectPackageMetadata($file);

                    if (!$this->packageMetadataRepository->getPackageByFilename($metadata->getFilename())) {
                        $this->packageMetadataRepository->insertPackageMetadata($metadata);
                    }
                } catch (Exception $exception) {
                    error_log($exception);
                }
            }
        }

        if (!$lastRelease) {
            return $response->withStatus(302)
                ->withHeader('Location', '/ui/subscription?error=No releases found');
        }

        $subscription = GitHubSubscription::create($owner, $name, $lastRelease);
        $this->subscriptionRepository->insert($subscription);

        return $response->withStatus(302)
            ->withHeader('Location', '/ui/subscription');
    }
}

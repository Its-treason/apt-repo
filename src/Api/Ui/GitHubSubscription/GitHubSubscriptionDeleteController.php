<?php

namespace ItsTreason\AptRepo\Api\Ui\GitHubSubscription;

use ItsTreason\AptRepo\Repository\GitHubSubscriptionRepository;
use ItsTreason\AptRepo\Value\GitHubSubscription;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GitHubSubscriptionDeleteController
{
    public function __construct(
        private readonly GitHubSubscriptionRepository $subscriptionRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getParsedBody();
        if (!isset($params['owner'], $params['name'])) {
            return $response->withStatus(302)
                ->withHeader('Location', '/ui/subscription?error=Missing values');
        }

        $subscription = GitHubSubscription::create($params['owner'], $params['name'], null);
        $this->subscriptionRepository->delete($subscription);

        return $response->withStatus(302)
            ->withHeader('Location', '/ui/subscription');
    }
}

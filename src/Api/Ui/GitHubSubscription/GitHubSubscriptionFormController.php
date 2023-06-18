<?php

namespace ItsTreason\AptRepo\Api\Ui\GitHubSubscription;

use ItsTreason\AptRepo\Repository\GitHubSubscriptionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

class GitHubSubscriptionFormController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly GitHubSubscriptionRepository $subscriptionRepository,
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $template = $this->twig->load('GitHubSubscription.twig');

        $subscriptions = $this->subscriptionRepository->getAll();
        $loggedIn = isset($request->getCookieParams()['apikey']);

        $body = $template->render([
            'subscriptions' => $subscriptions,
            'loggedIn' => $loggedIn,
        ]);
        $response->getBody()->write($body);

        return $response;
    }
}

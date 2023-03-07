<?php

namespace ItsTreason\AptRepo\Api\Ui;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function ItsTreason\AptRepo\Api\Login\setcookie;

class LoginActionController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (!isset($body['password'])) {
            return $response->withStatus(302)->withHeader('Location', '/ui/login');
        }

        $apiKey = getenv('API_KEY');
        if (hash('sha256', $apiKey) !== hash('sha256', $body['password'])) {
            return $response->withStatus(302)->withHeader('Location', '/ui/login');
        }

        setcookie('apikey', $apiKey, ['expires' => time() + 86400]);

        return $response->withStatus(302)->withHeader('Location', '/ui/upload');
    }
}

<?php

namespace ItsTreason\AptRepo\App\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookies = $request->getCookieParams();
        if (!isset($cookies['apikey'])) {
            return $this->returnRedirectResponse();
        }

        $apiKey = getenv('API_KEY');
        if (hash('sha256', $apiKey) !== hash('sha256', $cookies['apikey'])) {
            setcookie('apikey', null, ['expires' => time()]);
            return $this->returnRedirectResponse();
        }

        return $handler->handle($request);
    }

    private function returnRedirectResponse(): ResponseInterface
    {
        $response = new Response();
        return $response->withStatus(302)->withHeader('Location', '/ui/login');
    }
}

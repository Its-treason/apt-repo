<?php

namespace ItsTreason\AptRepo\App\Middleware;

use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class ErrorMiddleware implements ErrorRendererInterface
{
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        if ($exception->getMessage() === 'Not found.' && $exception->getCode() === 404) {
            return '404 - Not Found';
        }

        if (APP_ENV === 'production') {
            return '500 - An internal Server Error Occurred';
        }

        return <<<HTML
          <!DOCTYPE html>
          <html lang="en"><body>{$this->formatException($exception)}</body></html>
        HTML;
    }

    private function formatException(Throwable $exception): string
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $location = $exception->getFile() . ' Line: ' . $exception->getLine();
        $trace = $exception->getTraceAsString();
        $previous = 'None';
        if ($exception->getPrevious()) {
            $previous = $this->formatException($exception->getPrevious());
        }

        return <<<HTML
          <div>
            <br />
            <b>Message: </b> $message<br />
            <b>Code: </b> $code<br />
            <b>Location: </b> $location<br />
            <b>Trace:</b> <pre>$trace</pre>
            <b>Previous:</b> $previous
          </div>
        HTML;
    }
}

<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Middleware\Frontend;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;

class StopOutputMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($GLOBALS['TSFE']->applicationData['tx_pdfviewhelpers']['pdfOutput'] ?? false) {
            return new NullResponse();
        }

        return $response;
    }
}

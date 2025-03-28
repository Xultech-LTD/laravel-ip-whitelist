<?php
namespace Xultech\LaravelIpWhitelist\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedIpException extends HttpException
{
    /**
     * Create a new Unauthorized IP exception instance.
     *
     * @param string|null $message
     * @param int $statusCode
     */
    public function __construct(
        string $message = 'Access denied. Your IP is not authorized.',
        int $statusCode = 403
    ) {
        parent::__construct($statusCode, $message);
    }
}
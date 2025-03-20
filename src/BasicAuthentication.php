<?php
declare(strict_types = 1);

namespace Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BasicAuthentication extends HttpAuthentication implements MiddlewareInterface
{
    /** @var bool */
    private $verifyHash = false;

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $username = $this->login($request);

        if ($username === null) {
            return $this->responseFactory->createResponse(401)
                ->withHeader('WWW-Authenticate', sprintf('Basic realm="%s"', $this->realm));
        }

        if ($this->attribute !== null) {
            $request = $request->withAttribute($this->attribute, $username);
        }

        return $handler->handle($request);
    }

    public function verifyHash(bool $verifyHash = true): self
    {
        $this->verifyHash = $verifyHash;

        return $this;
    }

    /**
     * Check the user credentials and return the username
     */
    private function login(ServerRequestInterface $request): ?string
    {
        //Check header
        $authorization = $this->parseHeader($request->getHeaderLine('Authorization'));

        if (empty($authorization)) {
            return null;
        }

        //Check the user
        if (!isset($this->users[$authorization['username']])) {
            return null;
        }

        if ($this->verifyHash) {
            return password_verify($authorization['password'], $this->users[$authorization['username']])
                ? $authorization['username']
                : null;
        }

        return $this->users[$authorization['username']] === $authorization['password']
            ? $authorization['username']
            : null;
    }

    /**
     * Parses the authorization header for a basic authentication.
     *
     * @return ?array<string, string|null>
     */
    private function parseHeader(string $header): ?array
    {
        if (strpos($header, 'Basic') !== 0) {
            return null;
        }

        /** @var string|false $header */
        $header = base64_decode(substr($header, 6));

        if ($header === false) {
            return null;
        }

        $header = explode(':', $header, 2);

        return [
            'username' => $header[0],
            'password' => $header[1] ?? null,
        ];
    }
}

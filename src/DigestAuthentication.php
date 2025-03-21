<?php
declare(strict_types = 1);

namespace Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DigestAuthentication extends HttpAuthentication implements MiddlewareInterface
{
    /**
     * @var string|null The nonce value
     */
    private $nonce;

    /**
     * Set the nonce value.
     */
    public function nonce(string $nonce): self
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $username = $this->login($request);

        if ($username === null) {
            $header = sprintf(
                'Digest realm="%s",qop="auth",nonce="%s",opaque="%s"',
                $this->realm,
                $this->nonce ?: uniqid(),
                md5($this->realm)
            );

            return $this->responseFactory->createResponse(401)
                ->withHeader('WWW-Authenticate', $header);
        }

        if ($this->attribute !== null) {
            $request = $request->withAttribute($this->attribute, $username);
        }

        return $handler->handle($request);
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

        //Check whether user exists
        if (!isset($this->users[$authorization['username']])) {
            return null;
        }

        //Check authentication
        if (!$this->isValid($authorization, $request->getMethod(), $this->users[$authorization['username']])) {
            return null;
        }

        return $authorization['username'];
    }

    /**
     * Validates the authorization.
     *
     * @param array<string,string> $authorization
     */
    private function isValid(array $authorization, string $method, string $password): bool
    {
        $validResponse = md5(sprintf(
            '%s:%s:%s:%s:%s:%s',
            md5(sprintf('%s:%s:%s', $authorization['username'], $this->realm, $password)),
            $authorization['nonce'],
            $authorization['nc'],
            $authorization['cnonce'],
            $authorization['qop'],
            md5(sprintf('%s:%s', $method, $authorization['uri']))
        ));

        return $authorization['response'] === $validResponse;
    }

    /**
     * Parses the authorization header for a basic authentication.
     *
     * @return array<string, string>|null $header
     */
    private function parseHeader(string $header): ?array
    {
        if (strpos($header, 'Digest') !== 0) {
            return null;
        }

        $needed_parts = [
            'nonce' => 1,
            'nc' => 1,
            'cnonce' => 1,
            'qop' => 1,
            'username' => 1,
            'uri' => 1,
            'response' => 1,
        ];

        $data = [];
        $regexp = '@('.implode('|', array_keys($needed_parts)).')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@';

        preg_match_all($regexp, substr($header, 7), $matches, PREG_SET_ORDER);

        if ($matches) {
            foreach ($matches as $m) {
                // @phpstan-ignore-next-line
                $data[$m[1]] = $m[3] ?: $m[4];
                unset($needed_parts[$m[1]]);
            }
        }

        return empty($needed_parts) ? $data : null;
    }
}

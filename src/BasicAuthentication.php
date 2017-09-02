<?php

namespace Middlewares;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BasicAuthentication extends HttpAuthentication implements MiddlewareInterface
{
    /**
     * Process a server request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $username = $this->login($request);

        if ($username === false) {
            return Utils\Factory::createResponse(401)
                ->withHeader('WWW-Authenticate', sprintf('Basic realm="%s"', $this->realm));
        }

        if ($this->attribute !== null) {
            $request = $request->withAttribute($this->attribute, $username);
        }

        return $delegate->process($request);
    }

    /**
     * Check the user credentials and return the username or false.
     *
     * @param ServerRequestInterface $request
     *
     * @return false|string
     */
    private function login(ServerRequestInterface $request)
    {
        //Check header
        $authorization = $this->parseHeader($request->getHeaderLine('Authorization'));

        if (!$authorization) {
            return false;
        }

        //Check the user
        if (!isset($this->users[$authorization['username']])) {
            return false;
        }

        if ($this->users[$authorization['username']] !== $authorization['password']) {
            return false;
        }

        return $authorization['username'];
    }

    /**
     * Parses the authorization header for a basic authentication.
     *
     * @param string $header
     *
     * @return false|array
     */
    private function parseHeader($header)
    {
        if (strpos($header, 'Basic') !== 0) {
            return false;
        }

        $header = explode(':', base64_decode(substr($header, 6)), 2);

        return [
            'username' => $header[0],
            'password' => isset($header[1]) ? $header[1] : null,
        ];
    }
}

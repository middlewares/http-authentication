<?php

namespace Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Interop\Http\Middleware\DelegateInterface;

class DigestAuthentication extends HttpAuthentication implements ServerMiddlewareInterface
{
    /**
     * @var string|null The nonce value
     */
    private $nonce;

    /**
     * Set the nonce value.
     *
     * @param string $nonce
     *
     * @return self
     */
    public function nonce($nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }

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
            $header = sprintf(
                'Digest realm="%s",qop="auth",nonce="%s",opaque="%s"',
                $this->realm,
                $this->nonce ?: uniqid(),
                md5($this->realm)
            );

            return Utils\Factory::createResponse(401)
                ->withHeader('WWW-Authenticate', $header);
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

        //Check whether user exists
        if (!isset($this->users[$authorization['username']])) {
            return false;
        }

        //Check authentication
        if (!$this->isValid($authorization, $request->getMethod(), $this->users[$authorization['username']])) {
            return false;
        }

        return $authorization['username'];
    }

    /**
     * Validates the authorization.
     *
     * @param array  $authorization
     * @param string $method
     * @param string $password
     *
     * @return bool
     */
    private function isValid(array $authorization, $method, $password)
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
     * @param string $header
     *
     * @return false|array
     */
    private function parseHeader($header)
    {
        if (strpos($header, 'Digest') !== 0) {
            return false;
        }

        $needed_parts = [
            'nonce' => 1,
            'nc' => 1,
            'cnonce' => 1,
            'qop' => 1,
            'username' => 1,
            'uri' => 1,
            'response' => 1
        ];

        $data = [];
        $regexp = '@('.implode('|', array_keys($needed_parts)).')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@';

        preg_match_all($regexp, substr($header, 7), $matches, PREG_SET_ORDER);

        if ($matches) {
            foreach ($matches as $m) {
                $data[$m[1]] = $m[3] ? $m[3] : $m[4];
                unset($needed_parts[$m[1]]);
            }
        }

        return empty($needed_parts) ? $data : false;
    }
}

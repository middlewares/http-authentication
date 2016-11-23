<?php

namespace Middlewares\Tests;

use Middlewares\DigestAuthentication;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\CallableMiddleware;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;

class DigestAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testError()
    {
        $response = (new Dispatcher([
            (new DigestAuthentication(['user' => 'pass']))->realm('My realm')->nonce('xxx'),
        ]))->dispatch(new ServerRequest());

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame(
            sprintf('Digest realm="My realm",qop="auth",nonce="xxx",opaque="%s"', md5('My realm')),
            $response->getHeaderLine('WWW-Authenticate')
        );
    }

    public function testSuccess()
    {
        $nonce = uniqid();
        $request = (new ServerRequest([], [], '/'))
            ->withHeader('Authorization', $this->authHeader('user', 'pass', 'My realm', $nonce));

        $response = (new Dispatcher([
            (new DigestAuthentication(['user' => 'pass']))
                ->nonce($nonce)
                ->realm('My realm')
                ->attribute('auth-username'),

            new CallableMiddleware(function ($request) {
                $response = new Response();
                $response->getBody()->write($request->getAttribute('auth-username'));

                return $response;
            }),
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('user', (string) $response->getBody());
    }

    /**
     * @see https://tools.ietf.org/html/rfc2069#page-10
     *
     * @param string $username
     * @param string $password
     * @param string $realm
     * @param string $nonce
     * @param string $method
     * @param string $uri
     *
     * @return string
     */
    private function authHeader($username, $password, $realm, $nonce, $method = 'GET', $uri = '/')
    {
        $nc = '00000001';
        $cnonce = uniqid();
        $qop = 'auth';
        $opaque = md5($realm);

        $A1 = md5("{$username}:{$realm}:{$password}");
        $A2 = md5("{$method}:{$uri}");

        $response = md5("{$A1}:{$nonce}:{$nc}:{$cnonce}:{$qop}:{$A2}");
        $chunks = compact(
            'uri',
            'username',
            'realm',
            'nonce',
            'response',
            'qop',
            'nc',
            'opaque',
            'cnonce'
        );

        $header = [];
        foreach ($chunks as $name => $value) {
            $header[] = "{$name}=\"{$value}\"";
        }

        return 'Digest '.implode(', ', $header);
    }
}

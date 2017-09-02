<?php

namespace Middlewares\Tests;

use Middlewares\BasicAuthentication;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class BasicAuthenticationTest extends TestCase
{
    public function testError()
    {
        $response = Dispatcher::run([
            (new BasicAuthentication(['user' => 'pass']))->realm('My realm'),
        ]);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('Basic realm="My realm"', $response->getHeaderLine('WWW-Authenticate'));
    }

    public function testSuccess()
    {
        $request = Factory::createServerRequest()
            ->withHeader('Authorization', 'Basic '.base64_encode('user:pass'));

        $response = Dispatcher::run([
            (new BasicAuthentication(['user' => 'pass']))
                ->realm('My realm')
                ->attribute('auth-username'),

            function ($request) {
                echo $request->getAttribute('auth-username');
            },
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('user', (string) $response->getBody());
    }
}

<?php

namespace Middlewares\Tests;

use Middlewares\BasicAuthentication;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class BasicAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testError()
    {
        $request = Factory::createServerRequest();

        $response = (new Dispatcher([
            (new BasicAuthentication(['user' => 'pass']))->realm('My realm'),
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('Basic realm="My realm"', $response->getHeaderLine('WWW-Authenticate'));
    }

    public function testSuccess()
    {
        $request = Factory::createServerRequest()
            ->withHeader('Authorization', 'Basic '.base64_encode('user:pass'));

        $response = (new Dispatcher([
            (new BasicAuthentication(['user' => 'pass']))
                ->realm('My realm')
                ->attribute('auth-username'),

            function ($request) {
                echo $request->getAttribute('auth-username');
            },
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('user', (string) $response->getBody());
    }
}

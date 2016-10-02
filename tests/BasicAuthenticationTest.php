<?php

namespace Middlewares\Tests;

use Middlewares\BasicAuthentication;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use mindplay\middleman\Dispatcher;

class BasicAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testError()
    {
        $response = (new Dispatcher([
            (new BasicAuthentication(['user' => 'pass']))->realm('My realm'),
        ]))->dispatch(new ServerRequest());

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('Basic realm="My realm"', $response->getHeaderLine('WWW-Authenticate'));
    }

    public function testSuccess()
    {
        $request = (new ServerRequest())->withHeader('Authorization', 'Basic '.base64_encode('user:pass'));

        $response = (new Dispatcher([
            (new BasicAuthentication(['user' => 'pass']))
                ->realm('My realm')
                ->attribute('auth-username'),

            function ($request) {
                $response = new Response();
                $response->getBody()->write($request->getAttribute('auth-username'));

                return $response;
            },
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('user', (string) $response->getBody());
    }
}

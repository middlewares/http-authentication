<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use InvalidArgumentException;
use Middlewares\BasicAuthentication;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class BasicAuthenticationTest extends TestCase
{
    public function testException()
    {
        $this->expectException(InvalidArgumentException::class);

        $response = Dispatcher::run([
            new BasicAuthentication('foo'),
        ]);
    }

    public function testUserDoesNotExists()
    {
        $response = Dispatcher::run(
            [
                (new BasicAuthentication(['user' => 'pass']))->realm('My realm'),
            ],
            Factory::createServerRequest('GET', '/')
                ->withHeader('Authorization', 'Basic '.base64_encode('invalid-user:pass'))
        );

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testInvalidPassword()
    {
        $response = Dispatcher::run(
            [
                (new BasicAuthentication(['user' => 'pass']))->realm('My realm'),
            ],
            Factory::createServerRequest('GET', '/')
                ->withHeader('Authorization', 'Basic '.base64_encode('user:invalid-pass'))
        );

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testError()
    {
        $response = Dispatcher::run([
            (new BasicAuthentication(['user' => 'pass']))->realm('My realm'),
        ]);

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('Basic realm="My realm"', $response->getHeaderLine('WWW-Authenticate'));
    }

    public function testSuccess()
    {
        $request = Factory::createServerRequest('GET', '/')
            ->withHeader('Authorization', 'Basic '.base64_encode('user:pass'));

        $response = Dispatcher::run([
            (new BasicAuthentication(['user' => 'pass']))
                ->realm('My realm')
                ->attribute('auth-username'),

            function ($request) {
                echo $request->getAttribute('auth-username');
            },
        ], $request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('user', (string) $response->getBody());
    }

    public function testHashSuccess()
    {
        $request = Factory::createServerRequest('GET', '/')
            ->withHeader('Authorization', 'Basic '.base64_encode('user:rasmuslerdorf'));

        $response = Dispatcher::run([
            (new BasicAuthentication(['user' => '$2y$07$BCryptRequires22Chrcte/VlQH0piJtjXl.0t1XkA8pw9dMXTpOq']))
                ->verifyHash()
                ->realm('My realm')
                ->attribute('auth-username'),

            function ($request) {
                echo $request->getAttribute('auth-username');
            },
        ], $request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('user', (string) $response->getBody());
    }
}

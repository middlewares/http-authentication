<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\DigestAuthentication;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class DigestAuthenticationTest extends TestCase
{
    public function testError(): void
    {
        $response = Dispatcher::run([
            // @phpstan-ignore-next-line
            (new DigestAuthentication(['user' => 'pass']))->realm('My realm')->nonce('xxx'),
        ]);

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame(
            sprintf('Digest realm="My realm",qop="auth",nonce="xxx",opaque="%s"', md5('My realm')),
            $response->getHeaderLine('WWW-Authenticate')
        );
    }

    public function testUserDoesNotExists(): void
    {
        $response = Dispatcher::run(
            [
                // @phpstan-ignore-next-line
                (new DigestAuthentication(['user' => 'pass']))->realm('My realm')->nonce('xxx'),
            ],
            Factory::createServerRequest('GET', '/')
                ->withHeader('Authorization', $this->authHeader('invalid-user', 'pass', 'My realm', 'xxx'))
        );

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testInvalidPassword(): void
    {
        $response = Dispatcher::run(
            [
                // @phpstan-ignore-next-line
                (new DigestAuthentication(['user' => 'pass']))->realm('My realm')->nonce('xxx'),
            ],
            Factory::createServerRequest('GET', '/')
                ->withHeader('Authorization', $this->authHeader('user', 'invalid-pass', 'My realm', 'xxx'))
        );

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $nonce = uniqid();
        $request = Factory::createServerRequest('GET', '/')
            ->withHeader('Authorization', $this->authHeader('user', 'pass', 'My realm', $nonce));

        $response = Dispatcher::run([
            (new DigestAuthentication(['user' => 'pass']))
                ->nonce($nonce)
                ->realm('My realm')
                ->attribute('auth-username'),

            function ($request) {
                echo $request->getAttribute('auth-username');
            },
        ], $request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('user', (string) $response->getBody());
    }

    /**
     * @see https://tools.ietf.org/html/rfc2069#page-10
     */
    private function authHeader(
        string $username,
        string $password,
        string $realm,
        string $nonce,
        string $method = 'GET',
        string $uri = '/'
    ): string {
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

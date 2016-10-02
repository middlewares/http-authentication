# middlewares/http-authentication

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to implement [RFC 2617 Http Authentication](https://tools.ietf.org/html/rfc2617). Contains the following authentication methods:

* [Basic](https://en.wikipedia.org/wiki/Basic_access_authentication) Simplest technique.
* [Digest](https://en.wikipedia.org/wiki/Digest_access_authentication) More secure.

**Note:** This middleware is intended for server side only

## Requirements

* PHP >= 5.6
* A [PSR-7](https://packagist.org/providers/psr/http-message-implementation) http mesage implementation ([Diactoros](https://github.com/zendframework/zend-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), etc...)
* A [PSR-15](https://github.com/http-interop/http-middleware) middleware dispatcher ([Middleman](https://github.com/mindplay-dk/middleman), etc...)

## Installation

This package is installable and autoloadable via Composer as [middlewares/http-authentication](https://packagist.org/packages/middlewares/http-authentication).

```sh
composer require middlewares/http-authentication
```

## Example

```php
$dispatcher = new Dispatcher([
	new Middlewares\BasicAuthentication([
        'username1' => 'password1',
        'username2' => 'password2'
    ])
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Options

#### `__construct(array $users)`

Array with the usernames and passwords of all available users. The keys are the usernames and the values the passwords.

#### `realm(string $realm)`

The realm value. By default is "Login".

#### `attribute(string $attribute)`

The attribute name used to save the username of the user. If it's not defined, it wont be saved. Example:

```php
$dispatcher = new Dispatcher([
    (new Middlewares\BasicAuthentication([
        'username1' => 'password1',
        'username2' => 'password2'
    ]))->attribute('username'),

    function ($request) {
        $username = $request->getAttribute('username');

        return new Response('Hello '.$username);
    }
]);
```

#### `nonce(string $nonce)`

**Only available for Digest authentication.** To configure the nonce value. If its not defined, it's generated with [uniqid](http://php.net/uniqid)

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/http-authentication.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/http-authentication/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/http-authentication.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/http-authentication.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/36786f5a-2a15-4399-8817-8f24fcd8c0b4.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/http-authentication
[link-travis]: https://travis-ci.org/middlewares/http-authentication
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/http-authentication
[link-downloads]: https://packagist.org/packages/middlewares/http-authentication
[link-sensiolabs]: https://insight.sensiolabs.com/projects/36786f5a-2a15-4399-8817-8f24fcd8c0b4

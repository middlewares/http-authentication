# middlewares/http-authentication

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to implement [RFC 2617 Http Authentication](https://tools.ietf.org/html/rfc2617). Contains the following components:

* [BasicAuthentication](#basicauthentication)
* [DigestAuthentication](#digestauthentication)

## Requirements

* PHP >= 7.0
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/http-authentication](https://packagist.org/packages/middlewares/http-authentication).

```sh
composer require middlewares/http-authentication
```

## BasicAuthentication

The [Basic access authentication](https://en.wikipedia.org/wiki/Basic_access_authentication) is the simplest technique.

#### `__construct(array|ArrayAccess $users)`

`Array` or `ArrayAccess` with the usernames and passwords of all available users. The keys are the usernames and the values the passwords.

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

$response = $dispatcher->dispatch(new ServerRequest());
```

#### `responseFactory(Psr\Http\Message\ResponseFactoryInterface $responseFactory)`

A PSR-17 factory to create `401` responses.

## DigestAuthentication

The [Digest access authentication](https://en.wikipedia.org/wiki/Digest_access_authentication) is more secure than basic.

#### `__construct(array|ArrayAccess $users)`

`Array` or `ArrayAccess` with the usernames and passwords of all available users. The keys are the usernames and the values the passwords.

#### `realm(string $realm)`

The realm value. By default is "Login".

#### `attribute(string $attribute)`

The attribute name used to save the username of the user. If it's not defined, it wont be saved. Example:

#### `nonce(string $nonce)`

To configure the nonce value. If its not defined, it's generated with [uniqid](http://php.net/uniqid)

```php
$dispatcher = new Dispatcher([
    (new Middlewares\DigestAuthentication([
        'username1' => 'password1',
        'username2' => 'password2'
    ]))->attribute('username'),

    function ($request) {
        $username = $request->getAttribute('username');

        return new Response('Hello '.$username);
    }
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

#### `responseFactory(Psr\Http\Message\ResponseFactoryInterface $responseFactory)`

A PSR-17 factory to create `401` responses.

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/http-authentication.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/http-authentication/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/http-authentication.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/http-authentication.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/c2a3efcf-cf41-470a-bf56-84686972fe30.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/http-authentication
[link-travis]: https://travis-ci.org/middlewares/http-authentication
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/http-authentication
[link-downloads]: https://packagist.org/packages/middlewares/http-authentication
[link-sensiolabs]: https://insight.sensiolabs.com/projects/c2a3efcf-cf41-470a-bf56-84686972fe30

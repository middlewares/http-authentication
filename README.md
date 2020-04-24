# middlewares/http-authentication

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to implement [RFC 2617 Http Authentication](https://tools.ietf.org/html/rfc2617). Contains the following components:

* [BasicAuthentication](#basicauthentication)
* [DigestAuthentication](#digestauthentication)

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/http-authentication](https://packagist.org/packages/middlewares/http-authentication).

```sh
composer require middlewares/http-authentication
```

## BasicAuthentication

The [Basic access authentication](https://en.wikipedia.org/wiki/Basic_access_authentication) is the simplest technique.

You have to provide an `Array` or `ArrayAccess` with the usernames and passwords of all available users. The keys are the usernames and the values the passwords.

```php
Dispatcher::run([
    new Middlewares\BasicAuthentication([
        'username1' => 'password1',
        'username2' => 'password2'
    ])
]);
```

Optionally, you can provide a `Psr\Http\Message\ResponseFactoryInterface` as the second argument, that will be used to create the error responses (`401`). If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used to detect it automatically.

```php
$responseFactory = new MyOwnResponseFactory();

$route = new Middlewares\BasicAuthentication($users, $responseFactory);
```

### realm

The realm value. By default is "Login".

### attribute

The attribute name used to save the username of the user. If it's not defined, it wont be saved. Example:

```php
Dispatcher::run([
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

### verifyHash

This option verifies the password using [`password_verify`](https://www.php.net/manual/en/function.password-verify.php). Useful if you don't want to provide the passwords in plain text.

```php
$users = [
    'username' => password_hash('secret-password', PASSWORD_DEFAULT);
]

Dispatcher::run([
    (new Middlewares\BasicAuthentication($users))
        ->attribute('username')
        ->verifyHash(),

    function ($request) {
        $username = $request->getAttribute('username');

        return new Response('Hello '.$username);
    }
]);
```

## DigestAuthentication

The [Digest access authentication](https://en.wikipedia.org/wiki/Digest_access_authentication) is more secure than basic.

The constructor signature is the same than `BasicAuthentication`:

```php
$users = [
    'username1' => 'password1',
    'username2' => 'password2'
];
$responseFactory = new MyOwnResponseFactory();

Dispatcher::run([
    new Middlewares\DigestAuthentication($users, $responseFactory)
]);
```

### realm

The realm value. By default is "Login".

### attribute

The attribute name used to save the username of the user. If it's not defined, it wont be saved.

### nonce

To configure the nonce value. If its not defined, it's generated with [uniqid](http://php.net/uniqid)

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/http-authentication.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/http-authentication/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/http-authentication.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/http-authentication.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/http-authentication
[link-travis]: https://travis-ci.org/middlewares/http-authentication
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/http-authentication
[link-downloads]: https://packagist.org/packages/middlewares/http-authentication

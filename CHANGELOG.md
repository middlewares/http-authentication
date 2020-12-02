# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.1.1] - 2020-12-02
### Added
- Support for PHP 8

## [2.1.0] - 2020-04-24
### Added
- Option `BasicAuthentication::verifyHash()` [#2]

## [2.0.0] - 2019-11-30
### Added
- Added a second argument to the constructor to set a `ResponseFactory`

### Removed
- Support for PHP 7.0 and 7.1
- The option `responseFactory`. Use the second argument of the constructor.

## [1.1.0] - 2018-08-04
### Added
- PSR-17 support
- New option `responseFactory`

## [1.0.0] - 2018-01-25
### Added
- Improved testing and added code coverage reporting
- Added tests for PHP 7.2

### Changed
- Upgraded to the final version of PSR-15 `psr/http-server-middleware`

### Fixed
- Updated license year

## [0.5.0] - 2017-11-13
### Changed
- Replaced `http-interop/http-middleware` with  `http-interop/http-server-middleware`.

### Removed
- Removed support for PHP 5.x.

## [0.4.0] - 2017-09-21
### Added
- The `$users` argument of the constructor can be an `ArrayAccess`.

### Changed
- Append `.dist` suffix to phpcs.xml and phpunit.xml files
- Changed the configuration of phpcs and php_cs
- Upgraded phpunit to the latest version and improved its config file
- Updated to `http-interop/http-middleware#0.5`

## [0.3.0] - 2016-12-26
### Changed
- Updated tests
- Updated to `http-interop/http-middleware#0.4`
- Updated `friendsofphp/php-cs-fixer#2.0`

## [0.2.0] - 2016-11-22
### Changed
- Updated to `http-interop/http-middleware#0.3`

## 0.1.0 - 2016-10-02
First version

[#2]: https://github.com/middlewares/http-authentication/issues/2

[2.1.1]: https://github.com/middlewares/http-authentication/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/middlewares/http-authentication/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/middlewares/http-authentication/compare/v1.1.0...v2.0.0
[1.1.0]: https://github.com/middlewares/http-authentication/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/middlewares/http-authentication/compare/v0.5.0...v1.0.0
[0.5.0]: https://github.com/middlewares/http-authentication/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/middlewares/http-authentication/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/middlewares/http-authentication/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/middlewares/http-authentication/compare/v0.1.0...v0.2.0

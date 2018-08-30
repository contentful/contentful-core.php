# Changelog

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased](https://github.com/contentful/contentful-core.php/compare/1.5.0...HEAD)

## [1.5.0](https://github.com/contentful/contentful-core.php/tree/1.5.0) (2018-08-30)

### Added

* Class `ObjectHydrator` has been introduced, to abstract resource hydration.

## [1.4.0](https://github.com/contentful/contentful-core.php/tree/1.4.0) (2018-08-29)

### Added

* Coding standards are implemented in a generic way so that it can be reused in all SDKs.

## [1.3.0](https://github.com/contentful/contentful-core.php/tree/1.3.0) (2018-08-28)

### Added

* `LinkResolverInterface` was added to abstract link resolving.

## [1.2.0](https://github.com/contentful/contentful-core.php/tree/1.2.0) (2018-08-23)

### Changed

* The client used to create a log entry with a full message by default. Now it will create one regular entry with either level "INFO" or "ERROR" depending on the status code of the response, and one with level "DEBUG" with full dumps of request, response, and exception. 

## [1.1.0](https://github.com/contentful/contentful-core.php/tree/1.1.0) (2018-06-06)

### Fixed

* `Contentful\Core\Api\Location` used to provide a wrongful serialization of the longitude property. Now it correctly serializes to `lon` instead of `long`.

### Changed
* The `Contentful\Core\Log\Timer` has been deprecated and will be removed in version 2.

## [1.0.0](https://github.com/contentful/contentful-core.php/tree/1.0.0) (2018-04-17)

### Added

* Initial release.

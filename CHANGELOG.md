# Changelog

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased](https://github.com/contentful/contentful-core.php/compare/1.0.0...HEAD)

* `Contentful\Core\Api\Location` used to provide a wrongful serialization of the longitude property. Now it correctly serializes to `lon` instead of `long`.
* The `Contentful\Core\Log\Timer` has been deprecated and will be removed in version 2.

## [1.0.0](https://github.com/contentful/contentful-core.php/tree/1.0.0) (2018-04-17)

### Added

* Initial release.

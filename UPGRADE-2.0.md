# UPGRADE FROM 1.x to 2.0

## PHP Version

This SDK now requires PHP 7.0, which means all files now use the `strict_types` directive and scalar type declarations whenever possible.

## Change in BaseClient abstract methods

The class `Contentful\Core\Api\BaseClient` no longer requires the method `getSdkVersion` to be defined, but it now requires `getPackageName`, which should return the Packagist name (e.g. `contentful/core`). This name will then be used to automatically infer the SDK version number. This method, along with `getVersion`, `getSdkName`, and `getApiContentType`, is defined as static.

## Changes in BaseClient::request()

Abstract class `BaseClient`s now implements `Contentful\Core\Api\ClientInterface`, which means classes extending this must implement the `request` method. In the `BaseClient` the previous `request` method has been renamed `callApi`, and the `baseUri` option has been renamed `host`.

## Removal of Timer class

The class `Contentful\Core\Log\Timer` was deprecated in version 1.1 and has now been removed.

## Removal of third parameter in Query::where()

The method `Contentful\Core\Api\BaseQuery::where()` no longer accepts a third parameter. Now simply include that as part of the first parameter:

```php
// Before
$query->where('sys.id', $someId, 'ne');

// After
$query->where('sys.id[ne]', $someId);
```

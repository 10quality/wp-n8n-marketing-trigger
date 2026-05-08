# WordPress MVC (WPMVC) additional references

## Cache

Enable cache at `[project root path]/app/Config/app.php`:

```php
return [
    // OTHER KEYS...
    'cache' => [
        'enabled' => true,
    ],
    // OTHER KEYS...
];
```

### Usage

To use the cache, use the `Cache` class:

```php
use WPMVC\Cache;
```

#### Set a cache item

```php
Cache::add( 'cache_key', $value, 10 ); // 10 minutes
```

#### Get a cache item

```php
$value = Cache::get( 'cache_key', $defautl_value );
```

#### Delete a cache item
```php
Cache::forget( 'cache_key' );
```

#### Flush all cache items
```php
Cache::flush();
```

#### Remember pattern

```php
$value = Cache::remember( 'cache_key', 10, function() {
    // This callback will be executed if the cache item does not exist or is expired.
    return 'value';
} );
```

## Logger

Log storage:
```text
[project root path]/wp-content/wpmvc/logs
```

### Usage

To use the cache, use the `Log` class:

```php
use WPMVC\Log;
```

#### Log an info message

```php
Log::info( 'This is an info message' );
```

#### Log an error or exception

```php
Log::error( $e );
```

#### Debug a value

```php
Log::debug( 'display name', $values );
```

`$values` can be any mix of strings, or arrays (always cast object to arrays).
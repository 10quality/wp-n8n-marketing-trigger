# WordPress MVC (WPMVC) sanitization and request references

## Request handling

Use `WPMVC\Request` class to handle incoming data from `$_GET`, `$_POST` and `$wp_query->query_vars`:
```php
use WPMVC\Request;
```

### Usage

```php
$value = Request::input( 'name', $default_value );
```

## Handling WordPress nonce

To read and clear an incoming nonce set the third parameter to `true`:
```php
$nonce = Request::input( 'my_nonce', null, true );
```
Or if using PHP >= 8.0:
```php
$nonce = Request::input( key: 'my_nonce', clear: true );
```

## Disabled default sanitization

Set the fourth parameter to `false`:
```php
$value = Request::input( 'key', null, false, false );
```
Or if using PHP >= 8.0:
```php
$value = Request::input( key: 'key', sanitize: false );
```

## Custom sanitization

The `sanitize` or fourth parameter accepts a `callable`:
```php
$value = Request::input( 'key', null, false, function( $value ) {
    // Sanitization logic here
    return $value;
} );
```
Or if using PHP >= 8.0:
```php
$value = Request::input( key: 'key', sanitize: function( $value ) {
    // Sanitization logic here
    return $value;
} );
```
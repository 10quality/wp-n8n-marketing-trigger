# WordPress MVC (WPMVC) enqueue references

## Auto-enqueue

Auto-enqueue settings are configurable in the configuration file `[project root path]/app/Config/app.php`.

The PHP array key `autoenqueue` holds the settings for auto-enqueueing assets.

### Settings

Array key description:

* `'enabled'`: Enables or disables auto-enqueue
* `'priority'`: Priority of the auto-enqueue action hook, defaults to `10`.
* `'assets'`: Array of assets to auto-enqueue, each with:
  * `'asset'`: Path to the asset (e.g., `css/app.css`). Always points to a relative path inside `[project root path]/assets/`
  * `'dep'`: Array of dependencies (e.g., ['jquery'])
  * `'footer'`: Boolean; true loads in footer, false in header
  * `'id'`: (optional) asset id (slug name)
  * `'version'`: (optional) asset version, defaults to the project version
  * `'enqueue'` (optional) if `false`, registers but does not enqueue.
  * `'is_admin'` (optional) registers or enqueue only during WordPress admin dashboard

### Default

By default, the framework comes configured to enqueue the compiled files `css/app.css` and `js/app.js`.

## Example full auto enqueue

The following example shows how to properly enqueue an asset, for example `js/wpmvc.js`.

Inside `[project root path]/app/Config/app.php`.

```php
return [
    // OTHER KEYS...
    'autoenqueue' => [
        'enabled' => true,
        'priority' => 50,
        'assets' => [
            [
                'id' => 'wpmvc',
                'asset' => 'js/wpmvc.js',
                'dep' => ['jquery'],
                'footer' => true,
                'enqueue' => true,
            ],
        ],
    ],
    // OTHER KEYS...
]
```

## Example registar with delay enqueue

The following example shows how to properly reigster an asset, for example `js/wpmvc.js`.

Inside `[project root path]/app/Config/app.php`.

```php
return [
    // OTHER KEYS...
    'autoenqueue' => [
        'enabled' => true,
        'priority' => 10,
        'assets' => [
            [
                'id' => 'wpmvc',
                'asset' => 'js/wpmvc.js',
                'dep' => ['jquery'],
                'footer' => true,
                'enqueue' => false,
            ],
        ],
    ],
    // OTHER KEYS...
]
```

Then later in the code, for example inside a controller method, you can conditionally enqueue the asset:
```php
if (/* some condition */) {
    wp_localize_script( 'wpmvc', 'wpmvcData', [
        'someData' => 'value',
    ] );
    wp_enqueue_script( 'wpmvc' );
}
```
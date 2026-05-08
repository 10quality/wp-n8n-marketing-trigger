# WordPress MVC (WPMVC) front-end asset references

Any front-end file, non-PHP, is considered an asset in the framework; JavaScript, CSS, SASS, SCSS, LESS, fonts, images and views (templates) are examples of asset files.

## Raw vs compiled
Optimization and development are key factors empowered by the framework. Predefined Gulp tasks compile, minify, and concatenate raw assets for efficiency.

All CSS/JS/SASS files under `[project]/assets/raw` are subjected to compilation and/or concatenation. Files should be stored in the following subfolders:

* `[...]/assets/raw/css`: CSS files are concatenated into `app.css` and stored in `[..]/assets/css`. Subfolders (e.g., `admin`) generate corresponding files like `admin.css`.
* `[...]/assets/raw/js`: JS files are concatenated into `app.js` and stored in `[..]/assets/js`. Subfolders (e.g., `admin`) generate `admin.js`.
* `[...]/assets/raw/sass`: SASS/SCSS files are compiled into CSS and stored in `[..]/assets/css`.


## 3rd party / vendor asset injection

Downloaded `npm` dependencies need to be copied using **Gulp** inside the `[project]/assets/css` or `[project]/assets/js` folder to be accessible by the framework, then enqueued using the auto-enqueue settings.

### Example style

Importing `font-awesome` CSS files:

Create a custom Gulp task to copy the desired files from `node_modules` to the `[project]/assets/css` folder:

```js
gulp.task('vendorcss', async function() {
    return gulp.src([
        './node_modules/font-awesome/css/font-awesome.min.css',
        './node_modules/[other-vendor-file].css',
        './node_modules/[other-vendor-file].css',
    ])
    .pipe(gulp.dest('./assets/css'));
});
```

Custom gulp tasks must be added to the file [project]/package.json so these are registered and taken into account during compilation and deployment, see the example below:
```json
{
  "prestyles": [
    "sass",
    "vendorcss"
  ],
}
```

Then added to auto-enqueue:
```php
'autoenqueue' => [
    'enabled' => true,
    'priority' => 50,
    'assets' => [
        [
            'id' => 'font-awesome',
            'asset' => 'css/font-awesome.min.css',
            'version' => ['4.7.0'],
            'footer' => false,
            'enqueue' => true,
        ],
    ],
],
```

### Example script

Copying `select2` JS files:

Create a custom Gulp task to copy the desired files from `node_modules` to the `[project]/assets/js` folder:

```js
gulp.task('vendorsjs', async function() {
    return gulp.src([
        './node_modules/select2/dist/js/select2.min.js',
        './node_modules/[other-vendor-file].js',
        './node_modules/[other-vendor-file].js',
    ])
    .pipe(gulp.dest('./assets/js'));
});
```

Custom gulp tasks must be added to the file `[project]/package.json` so these are registered and taken into account during compilation and deployment, see the example below:
```json
{
  "prescripts": [
    "vendorsjs"
  ],
}
```

Then added to auto-enqueue:
```php
'autoenqueue' => [
    'enabled' => true,
    'priority' => 50,
    'assets' => [
        [
            'id' => 'select2',
            'asset' => 'js/select2.min.js',
            'version' => ['4.0.13'],
            'footer' => true,
            'enqueue' => true,
        ],
    ],
],
```

## Views

HTML files are considered views in the framework, and they are stored in the `[project root path]/assets/views/` folder. Views can be rendered from controllers, from other views, or from other PHP code.


### Storage path

`[project root path]/assets/views/`

### Usage

#### Within a controller

Use controller's view property:
```php
// To print the view
$this->view->show( 'view-key', [
    'key' => 'value',
] );
// To get the view as HTML string
$html = $this->view->get( 'view-key', [
    'key' => 'value',
] );
```

#### Within a view

Use the `get_bridge` function:
```html
<div>
    <?php get_bridge( 'Namespace' )->view( 'view-key', [
        'key' => 'value',
    ]  ) ?>
</div>
```

**NOTE:** It is recommended to autoload a global functions file using composer to add a custom view wrapper, like:
```php
// To get the view
function namespace_get_view( string $view_key, ?array $data = [] ) {
    return get_bridge( 'Namespace' )->get_view( $view_key, $data );
}
// To print the view
function namespace_show_view( string $view_key, ?array $data = [] ) {
    get_bridge( 'Namespace' )->view( $view_key, $data );
}
```
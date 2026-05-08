# WordPress MVC (WPMVC) references

## Online Documentation reference

- [Wordpress MVC (WPMVC) framework documentation](https://10quality.github.io/wpmvc/)

## Installation

1. Requirements: composer, Node.js >= 24, npm and Gulp.
2. Run the CLI command:
    ```bash
    composer create-project --no-plugins 10quality/wpmvc .
    ```
3. Run the following command to install the PHP back-end dependencies:
    ```bash
    composer install
    ```
4. Run the following command to install the NPM fron-end dependencies:
    ```bash
    npm install
    ```
5. Run setup command:
    ```bash
    php ayuco setup
    ```
6. Follow the instructions, indicate if the project is a plugin or theme, set a project name, set a namespace (always use a single word namespace without '\\'), set a text domain (use the slug version of the root folder path), set an author and license.
7. Remove any instance of the `.gitkeep` file.

## File structure

```text
my-awesome-plugin-or-theme/                 # Root folder (plugin folder name or theme folder)
├── assets/                                 # All static frontend and admin files
│   ├── css/                                # Compiled CSS files
│   ├── js/                                 # Compiled JavaScript files
│   ├── img/                                # Images, icons, SVGs
│   ├── fonts/                              # Custom fonts
│   ├── lang/                               # Translation files (.pot, .po, .mo)
│   ├── raw/                                # Source files for asset compilation (Gulp)
│   │   ├── css/                            # Raw CSS before concatenation/minification
│   │   ├── js/                             # Raw JavaScript before bundling
│   │   └── sass/                           # SCSS/SASS source files
│   └── views/                              # View files
├── app/                                    # Core application code — the MVC heart (PSR-4)
│   ├── Config/                             # Configuration files
│   │   └── app.php                         # Main app settings (namespace, version, cache, autoenqueue, localize…)
│   ├── Controllers/                        # MVC Controllers
│   │   ├── PublicController.php            # Public-facing logic
│   │   └── Admin/                          # Admin-area controllers
│   ├── Models/                             # Data models (post, user, term, option, comment, custom tables)
│   ├── Widgets/                            # (OPTIONAL) Custom widget classes
│   └── Main.php                            # Main.php (hooks router)
├── tests/                                  # PHPUnit tests (if you ran `php ayuco setup tests`)
├── composer.json                           # Composer dependencies & autoloading
├── package.json                            # npm scripts & Gulp dependencies
├── gulpfile.js                             # Asset compilation & build tasks
├── ayuco                                   # Ayuco CLI executable (make sure chmod +x ayuco)
├── functions.php                           # (THEME ONLY) Main theme entry file (minimal bootstrap)
├── style.css                               # (THEME ONLY) Main theme indentifier file (minimal bootstrap)
├── plugin.php                              # (PLUGIN ONLY) Main plugin entry file (minimal bootstrap)
└── README.md                               # Standard WordPress plugin readme
```

## CLI Commands

WordPress MVC framwork (WPMVC) comes built-in with its own CLI `ayuco`.

### Handlers

Common command handlers `{handler}` are:
* **Controller handler:** `{controller class}@{method}`: where `{controller class}` refers to a PHP class name (without namespace, as it self-implied) and `{method}` refers to a method inside the PHP class. For example: `UserController@register`.
  * Controller handler creates a controller class if not exist at `[project root path]/app/Controllers/{controller class}.php`.
  * Controller handler creates a class method if not exist inside `[project root path]/app/Controllers/{controller class}.php`.
    * The method doesn't include parameters, you must modify them manually if specified by the desired WordPress hook.
* **View handler:** `view@{view key}`: where `{view key}` refers to a PHP view. For example: `view@shortcodes.hello-world`.
  * View handlers creates a view at `[project root path]/assets/views/{view key}`.
  * A dot `.` inside the key indicates the relative path and filename of the view, for example `path.to.view` translates to `/path/to/view.php`.

## Adding WordPres hooks

### Add an action hook

Use the command:
```bash
php ayuco add action:{hook} {handler} --nopretty
```

Where:
* `{hook}` is the name of the action hook.
* `{handler}` is the name of the handler controller or a view handler.

### Add a filter hook

Use the command:
```bash
php ayuco add filter:{hook} {handler} --nopretty
```

Where:
* `{hook}` is the name of the filter hook.
* `{handler}` is the name of the handler controller or a view handler.

### Adding WordPres shortcodes

Use the command:
```bash
php ayuco add shortcode:{shortcode} {handler} --nopretty
```

Where:
* `{shortcode}` is the name of the shortcode.
* `{handler}` is the name of the handler controller or a view handler.

### Adding WordPres widget

Use the command:
```bash
php ayuco add widget:{widget} --nopretty
```

Where:
* `{widget}` is the class name of the widget.

### Hook definitions

All hook definitions are located at `[project root path]/app/Main.php`.

#### Modiying or editing a hook definition

To modify or edit a hook definition:
1. Edit the PHP code at `[project root path]/app/Main.php` file.
2. Edit the handler file specified by the hook.

## Create data models

### Post based models

Use the command:
```bash
php ayuco create model:{model} {type} --nopretty
```

Where:
* `{model}` is the name of the model class.
* `{type}` (optional) is the post type of the model, for example `post`.

### Term (taxonomy) based models

Use the command:
```bash
php ayuco create termmodel:{model} {taxonomy} --nopretty
```
Where:
* `{model}` is the name of the model class.
* `{type}` (optional) is the taxonomy related to the model. Do not use this argument if the model will be a generic model that handles different taxonomies.

### User based models

Use the command:
```bash
php ayuco create usermodel:{model} --nopretty
```
Where:
* `{model}` is the name of the model class.

### User based models

Use the command:
```bash
php ayuco create commentmodel:{model} --nopretty
```
Where:
* `{model}` is the name of the model class.

### Option based models

Use the command:
```bash
php ayuco create optionmodel:{model} {id} --nopretty
```
Where:
* `{model}` is the name of the model class.
* `{id}` Option name in the database.

## Assets

### Create a JavaScript asset

Use the command:
```bash
php ayuco create js:{filename}
```

Where:
* `{filename}` is the name of the JavaScript file to create at `[project root path]/assets/raw/js/{filename}.js`.

### Create a JavaScript asset

Use the command:
```bash
php ayuco create css:{filename}
```

Where:
* `{filename}` is the name of the JavaScript file to create at `[project root path]/assets/raw/css/{filename}.css`.

### Create a SCSS asset

Use the command:
```bash
php ayuco create scss:{master or part} {master}
```

Where:
* `{master or part}` is the name of a master file (see description below) or the name of a part/partial file.
* `{master}`` (optional) is the name of the master file to import into.

### Compile raw assets

Use the command:
```bash
npm run dev
```

### CSS, JavaScript, SASS, SCSS

Steps to add an asset and compile it:
1. Create the asset using the command `php ayuco create {type}:{filename}`.
2. Run the command `npm run dev` to compile the asset and its dependencies.

* Raw non compiled assets are located at `[project root path]/assets/raw`.
* Compiled assets are located at `[project root path]/assets`.

## Versioning

To change the version of the project use the command:
```bash
php ayuco set version:{version}
```

Where:
* `{version}` is the new version of the project, for example `1.0.0`.

## Namespace change

To change the version of the project use the command:
```bash
php ayuco set namespace:{namespace}
```

Where:
* `{namespace}` is the new namespace of the project, for example `MyAwesomePlugin`.

## Text domain change

To change the text domain of the project use the command:
```bash
php ayuco set domain:{domain}
```

Where:
* `{domain}` is the new text domain of the project, for example `my-awesome-plugin`.

## Change the project author

Use the command to call the prompt wizard:
```bash
php ayuco set author
```

## Change the project license

Use the command to call the prompt wizard:
```bash
php ayuco set license
```

## Localization support

Enable localize at `[project root path]/app/Config/app.php`.

### Generate POT file

Use the command:
```bash
php ayuco generate pot
```

### Generate PO file

Use the command:
```bash
php ayuco generate po:{locale}
```

Where:
* `{locale}` is the locale of the PO file to generate, for example `es_ES`.

### Generate PO and MO file

Use the command:
```bash
php ayuco generate mo:{locale}
```

Where:
* `{locale}` is the locale of the MO file to generate, for example `es_ES`.

### Generate POT, PO, MO and translate

Use the command to generate or update the POT, PO, and MO files; and call to the CLI wizard for string translations:
```bash
php ayuco generate translations:{locale}
```

Where:
* `{locale}` is the locale of the translation strings to generate, for example `es_ES`.

## Build

### Generate a deployable ZIP

Use the command:
```bash
npm run build
```

## Autoload

* Composer will autoload the PHP code located at `[project root path]/app` under the project namespace specified at `app/Config.php` using PSR-4.
* WordPress loads the file:
  * For plugins `[project root path]/plugin.php` (this file calls composer autoloader).
  * For themes `[project root path]/functions.php` (this file calls composer autoloader).
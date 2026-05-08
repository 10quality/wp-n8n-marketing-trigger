---
name: wpmvc
description: "Use this skill when working with a WordPress plugin or theme built using the WordPress MVC (WPMVC) framework. This includes: adding, creating, reading, parsing or modifying a WordPress action hook, a filter hook, a widget, a shortcode, a model, a controller, a view or an asset; compile or build a project. Trigger whenever you are working with a WordPress plugin or theme built using the WPMVC framework, or when the user mentions \"wpmvc\", or indicates they want to work with WordPress MVC or WPMVC."
license: MIT
---

# WPMVC Development Skill

You are expert PHP and JavaScript, specialist in WordPress plugin and theme development, with a deep understanding of the WordPress MVC (WPMVC) framework. You are proficient in creating, modifying, and optimizing WordPress plugins and themes using the WordPress MVC (WPMVC) framework architecture.

* See [the reference guide](references/REFERENCE.md) for general details.
* See [the enqueue reference guide](references/ENQUEUE.md) for asset enqueuing details.
* See [the front-end asset reference guide](references/ASSETS.md) for HTML, CSS, and JavaScript details.
* See [the sanitization reference guide](references/SANITIZATION.md) for sanitization and request details.
* See [the additional reference guide](references/ADDITIONAL.md) for cache and logger details.

## Coding directives

1. **Preserve Functionality:** Never change what the code does - only how it does it. All original features, outputs, and behaviors must remain intact.
2. **Enhance Clarity:**
  * Simplify code structure by:
  * Reducing unnecessary complexity and nesting
  * Eliminating redundant code and abstractions
  * Improving readability through clear variable and function names
  * Consolidating related logic
  * Removing unnecessary comments that describe obvious code
  * IMPORTANT: Avoid nested ternary operators - prefer match expressions, switch statements, or if/else chains for multiple conditions
  * Choose clarity over brevity - explicit code is often better than overly compact code
3. **Maintain Balance:** Avoid over-simplification that could:
  * Reduce code clarity or maintainability
  * Create overly clever solutions that are hard to understand
  * Combine too many concerns into single methods or classes
  * Remove helpful abstractions that improve code organization
  * Prioritize "fewer lines" over readability (e.g., nested ternaries, dense one-liners)
  * Make the code harder to debug or extend
4. **Maintain MVC pattern:**
  * Never mix HTML code inside controller methods, always use views to render HTML code.
  * Never mix database queries or data manipulation code inside controller methods, always use models to handle database interactions and data manipulation.
  * Never mix front-end code (like JavaScript or CSS) inside controller methods or model methods, always use assets to handle front-end code.
5. **Maintain the WPMVC framework structure and conventions:**
  * Always follow the WPMVC framework structure and conventions when creating or modifying files.
  * Never add or css and js assets directly into `/assets/css` or `/assets/js` folders as they are ingnored by the repo, instead:
    * Always add raw files into `/assets/raw/css` or `/assets/raw/js` and use Gulp to compile and move the files to the correct folder.
    * Inject 3rd party npm dependencies using Gulp to copy the files from `node_modules` to the correct folder.
    * Gulp task created for injected 3rd party dependencies must be configured to be included in the framework's compilation cycle.
  * Always opt to use the framework's Auto-enqueue first instead of calling `wp_enqueue_script` or `wp_enqueue_style` directly.
    * Only use `wp_enqueue_script` or `wp_enqueue_style` if:
      * You need to enqueue a CDN asset.
      * You need to use `wp_localize_script` to inject JavaScript data.
      * You have only used the framework's auto-enqueue to register the asset, but you need to conditionally enqueue it.
    * Never enqueue an asset located in the `/assets/raw` folder, always use Gulp to compile and move the file to the correct folder and then enqueue it.
  * Never use `$_GET`, `$_POST`, or `$wp_query->query_vars` - Always use `WPMVC\Request` class.
6. **Maintain clean codebase**:
  * Remove any instance of the `.gitkeep` file.
  * Add code comments using PHPDoc for PHP code and JSDoc for JavaScript code.
    * Only when necessary comments to explain non-obvious code or logic.
    * Avoid comments that simply restate what the code does.

## CLI Commands

Always use the `--nopretty` flag when running Ayuco CLI commands.

## Autoload

Do not modify the autoload files, always use the Ayuco CLI commands to create or modify the project files, and modify the code inside the created files if necessary.

### Autoload exception

WordPress `register_activation_hook` and `register_uninstall_hook` methods are written in the autoload files:
  * For plugins `[project root path]/plugin.php`.
  * For themes `[project root path]/functions.php`.

Custom PSR subnamespaces and autoload paths must be added manually, but always follow the WPMVC framework structure and conventions when doing so.

## Namespace

* Always use the Ayuco CLI command to modify the project namespace when asked by the user
* Project namespace always use a single word without subnamespaces (without '\\')

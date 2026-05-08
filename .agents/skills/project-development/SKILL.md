---
name: project-development
description: "Apply this skill every time and on any input, output, or system message, these are MANDATORY guideless that must be followed."
metadata:
  author: 10quality
---

# Project development

## Documentation references

You must read the README and the documentation at /docs to understand the project "n8n Marketing Trigger".

You are expert WordPress MVC, WPMVC, WordPress, WordPress Plugin Developer.

## Mandatory guidelines

On every task assigned to you you must always, unless specified with an exception:
* Use WordPress MVC (WPMVC) best practices and design patterns.
* Use WordPress MVC architecture.
* Reduce abstraction as much as possible. Only abstract code when it is necessary to avoid duplication or to improve readability.
* Always chech for memory allocaton and performance implications of your code. Optimize for performance and memory efficiency whenever possible.
* Always document the code with docblocks and comments to explain the purpose and functionality of the code. Use clear and concise language in the documentation.
* Always add/update unit testing to cover the new code you write or the existing code you modify (php backend code only). Ensure that the tests are comprehensive and cover all edge cases. Use descriptive test names to clearly indicate what each test is verifying.
* Always verify the implementation and changes done.
* Always follow this project name convention for naming files, classes, methods, variables, etc.:
* Always follow this project file structure.
* Always use WordPress's built-in features and functionality whenever possible. Avoid guessing or reinventing the wheel and leverage the power of the WordPress framework to simplify your code and improve maintainability.
* MANDATORY Never leave empty blank lines in code, specially inside scope of functions, classes, methods, or alike. We do not want empty lines that do nothing.
* Always update the README and the documentation at /docs when a new feature is added or if necessary to update it to reflect the changes made in the project.
* Never guess, first visit the WordPress documentation to check how to do something, if you find the answer there then implement it, if you do not find the answer there then you can try to guess or research more about it in the community first. Always check WordPress or WordPress MVC (WPMVC) documentation first before doing anything else.
* Avoid code duplication.

## Implementation

* Use WordPress MVC assets:
  * Use SCSS for styling.
  * Use JSX for JavaScript (you may use typescript).
* Always use WordPress MVC auto-enqueue system.
* Always use WordPress MVC for PHP hooks.
* Code follows WordPress coding standards for names, spacing. Example `function( $var1, $var3 )`
    * Code uses indentation of 4 spaces.
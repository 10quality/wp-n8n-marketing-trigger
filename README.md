# n8n Marketing Trigger - WordPress Plugin

Built using the [WordPress MVC](https://10quality.github.io/wpmvc/) (WPMVC) framework.
This plugin helps teams orchestrate AI-assisted marketing campaign workflows with n8n directly from WordPress.
You define campaign content in WordPress, then trigger n8n webhooks so your automations can receive structured campaign data and continue execution.

## Purpose

n8n Marketing Trigger connects WordPress campaign authoring with n8n workflow automation.
It gives editors a dedicated campaign workspace in wp-admin and sends campaign payloads to n8n webhook endpoints, allowing downstream workflow steps such as content processing, AI generation, and multi-platform publishing flows.

## Features

- Settings page under `Settings > Marketing campaigns`.
- Campaign custom post type (`marketing_campaign`) with localized admin labels.
- Campaign metaboxes for platforms, cover image options, and trigger actions.
- Trigger buttons for test and production sends from the campaign editor.
- AJAX-based webhook delivery to n8n endpoints using structured JSON payloads.
- Inline trigger feedback in the editor with upstream webhook error details.
- Payload preview tab with example JSON structure.
- Spanish localization catalogs for WordPress Spanish locale variants.

## Install

Download composer dependencies:

```bash
composer install --no-plugins
```

Download npm/node dependencies:

```bash
npm install
```

Install Gulp:

```bash
npm install -g gulp-cli
```

Run setup wizard:

```bash
php ayuco setup --nopretty
```

## Test Guide

PHPUnit is configured through `phpunit.xml` and `tests/bootstrap.php`.

In this project, tests are expected to run inside the WordPress Docker container:

```bash
docker ps
docker exec -it 10quality-wordpress-1 bash
cd wp-content/plugins/n8n-marketing-trigger
./vendor/bin/phpunit
```

## Docs

See [docs/marketing-trigger-guide.md](docs/marketing-trigger-guide.md) for full behavior and acceptance mapping.

## License

MIT License - (c) 2026 [10 Quality](https://10quality.studio).

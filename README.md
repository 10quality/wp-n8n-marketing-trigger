# n8n Marketing Trigger - WordPress Plugin

Built using the [WordPress MVC](https://10quality.github.io/wpmvc/) (WPMVC) framework.

## Features

- Settings page under `Settings > Marketing Trigger Settings`.
- Campaign custom post type (`marketing_campaign`) with campaign and trigger metaboxes.
- Webhook sending via Guzzle to test and production URLs.
- Payload preview tab with JSON payload example.

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
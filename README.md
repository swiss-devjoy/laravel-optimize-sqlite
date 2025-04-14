# Optimize your SQLite database for production in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/swiss-devjoy/laravel-optimize-sqlite.svg?style=flat-square)](https://packagist.org/packages/swiss-devjoy/laravel-optimize-sqlite)
[![Total Downloads](https://img.shields.io/packagist/dt/swiss-devjoy/laravel-optimize-sqlite.svg?style=flat-square)](https://packagist.org/packages/swiss-devjoy/laravel-optimize-sqlite)

> This package is already used in production in multiple projects.
> Still, if you want to use this package, **always** backup your database before requiring it through Composer.

This package optimizes your SQLite database for production in Laravel. The settings are applied as soon as any sqlite database connection is established, so you can use them in your application right away.

The current settings are:

```
 ┌───────────────────────────┬─────────────────────┬
 │ Setting                   │ Value               │
 ├───────────────────────────┼─────────────────────┼
 │ PRAGMA auto_vacuum        │ incremental         │
 │ PRAGMA journal_mode       │ WAL                 │
 │ PRAGMA page_size          │ 32768 (32 KB)       │
 │ PRAGMA busy_timeout       │ 5000 (5 seconds)    │
 │ PRAGMA cache_size         │ -20000              │ 
 │ PRAGMA foreign_keys       │ ON                  │
 │ PRAGMA mmap_size          │ 134217728 (128 MB)  │
 │ PRAGMA temp_store         │ MEMORY              │
 │ PRAGMA synchronous        │ NORMAL              │
 └───────────────────────────┴─────────────────────┘
 ```

## Installation

You can install the package via composer:

```bash
composer require swiss-devjoy/laravel-optimize-sqlite
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-optimize-sqlite-config"
```

This is the contents of the published config file:

```php
return [
    'general' => [
        'journal_mode' => 'WAL',
        'auto_vacuum' => 'incremental',
        'page_size' => 32768, // 32 KB
        'busy_timeout' => 5000, // 5 seconds
        'cache_size' => -20000,
        'foreign_keys' => 'ON',
        'mmap_size' => 134217728, // 128 MB
        'temp_store' => 'MEMORY',
        'synchronous' => 'NORMAL',
    ],

    // You can override the general settings for specific database connections, defined in config/database.php
    'databases' => [
        'example_connection' => [
            'busy_timeout' => 10000, // override general settings and set 10 seconds
            'temp_store' => null, // unset temp_store from general settings
        ],
    ],
];
```

You can set general settings for all sqlite connections, and override them for specific connections.

## Important notes

- There is a known issue with databases already in WAL mode and running php artisan migrate:fresh which will throw an error, see https://github.com/laravel/framework/discussions/53044
  My current alternative is to run a custom composer command (`composer.json`) which refreshes the whole app:
  ```
    "scripts": {
        "dev-migrate": [
            "Composer\\Config::disableProcessTimeout",
            "rm -f database/*.sqlite",
            "touch database/database.sqlite",
            "@php artisan migrate --seed --ansi",
            "@php artisan cache:clear"
        ]
    },
  ```
- Laravel allows already to set some `PRAGMA` settings in config/database.php which will override the settings in this package. You might want to set those settings to `null` and use this config as sole single source of truth for sqlite database settings.
- I noticed that running multiple PRAGMA statements in a single query (e.g. `PRAGMA journal_mode=WAL; PRAGMA auto_vacuum=incremental;`) does not work as expected. The settings are not applied. This is why I decided to run each setting in a separate query.

## Inspiration

The general ideas are from Nuno Maduro's package https://github.com/nunomaduro/laravel-optimize-database.
For more information about possible settings you can look into the SQLite documentation: https://www.sqlite.org/pragma.html

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Dimitri König](https://showcaseful.com/dimitrikoenig)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

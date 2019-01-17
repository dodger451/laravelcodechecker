# LaravelCodeChecker

[![StyleCI](https://github.styleci.io/repos/154905196/shield?branch=master)]

Creates laravel artisan commands to validate your application's sourcecode with `php -l`, `phpmd` and `phpcs`.

Publishes predefined style rulesets into `config/` for customization.

## Installation

Install via Composer

``` bash
$ composer require --dev dodger451/laravelcodechecker
```

Then copy the default config files to /config by running

``` bash
php artisan vendor:publish --provider=dodger451/laravelcodechecker
```

This will create some rulesets for phpmd and phpcs in ``config/``
``` bash
config/
    laravelcodechecker.php
    phpcs/
        ruleset.xml
    phpmd/
        rulesets/
            cleancode.xml
            codesize.xml
            controversial.xml
            design.xml
            naming.xml
            unusedcode.xml
```
Adopt the rules in `phpcs/ruleset.xml` and `phpmd/rulesets/*.xml` to your preferences, the `cc:` commands will use them per default.
To change the default rulefiles, modify ``config/laravelcodechecker.php``

## Usage

To validate the application with all checks, run
``` bash
php artisan cc:all
```

This will run `php -l`, `phpmd` and `phpcs` on all .php files in `tests/` `routes/` `config/` and `app/`.


Alternatively, run each separately:

``` bash
php artisan cc:phplint
php artisan cc:phpcs
php artisan cc:phpmd
```

Per default, each command will target ``tests routes config app``.
You can provide dirs and/or files as arguments to each `cc:` command,
if you don't want to use the default targets in ``config/laravelcodechecker.php``

E.g. to limit phpmd to the directories `tests/` and `app/` run
``` bash
php artisan cc:phpmd tests app
```
To change the default targets per command, modify ``laravelcodechecker.php``

### Travis
Example `.travis.yml` for travis-ci.org
```
language: php
php:
  - '7.1'
  - '7.2'
  - nightly

install:
  - composer install

script:
  - php artisan cc:all
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [david latotzky][https://www.linkedin.com/in/david-latotzky/]

## License

license. Please see the [license file](license.md) for more information.

[link-styleci]: https://styleci.io/repos/154905196)]
[link-author]: https://github.com/dodger451

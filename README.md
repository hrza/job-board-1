Job Board
================

Simple job publishing/review app that allows HR managers to post jobs and moderators to approve/deny posted jobs.

Bellow you can find more details on installation of app.


Setup
-----------------

### Script

If you are on UNIX and composer is installed globally, the easiest way is to run from project root

```bash
 $ ./bin/setup.sh
```

### Manual

Run composer install and set up parameters

```bash
$ composer install
```

Create database

```bash
$ bin/console doctrine:database:create
```
Execute migrations

```bash
 bin/console doctrine:migrations:migrate
```

Tests
-------

PHPSpec is used for testing. Currently only few stuff is covered, but you can check out:

``` bash
$ vendor/bin/phpspec run
```


# Quillstack Local Storage

[![Tests](https://github.com/quillstack/local-storage/actions/workflows/tests.yml/badge.svg)](https://github.com/quillstack/local-storage/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/quillstack/local-storage.svg)](https://packagist.org/packages/quillstack/local-storage)
[![Downloads](https://img.shields.io/packagist/dt/quillstack/local-storage.svg)](https://packagist.org/packages/quillstack/local-storage)
[![PHP Version](https://img.shields.io/packagist/php-v/quillstack/local-storage)](https://packagist.org/packages/quillstack/local-storage)
[![StyleCI](https://github.styleci.io/repos/394779968/shield?branch=main)](https://github.styleci.io/repos/394779968?branch=main)
[![CodeFactor](https://www.codefactor.io/repository/github/quillstack/local-storage/badge)](https://www.codefactor.io/repository/github/quillstack/local-storage)
[![Maintainability](https://api.codeclimate.com/v1/badges/d3fbd09f1580534b0c0e/maintainability)](https://codeclimate.com/github/quillstack/local-storage/maintainability)
[![License](https://img.shields.io/packagist/l/quillstack/local-storage)](https://github.com/quillstack/local-storage/blob/main/LICENSE)

The package to manage files on the local storage.

### Installation

To install this package, run the standard command using _Composer_:

```
composer require quillstack/local-storage
```

### Usage

Create a class or inject it as a dependency:

```php
use Quillstack\LocalStorage\LocalStorage;

$storage = new LocalStorage();
$storage->save('var/cache/token.txt', 'muHaloosPps23sKkdsaaBBcei');
```

If you want to use it as a dependency:

```php
use Quillstack\LocalStorage\LocalStorage;

public function __construct(private LocalStorage $storage)
{
    //
}

public function getTokenFromCache()
{
    $this->storage->get('var/cache/token.txt');
}
```

### Storage interface

This package implements `quillstack/storage-interface`: \
https://github.com/quillstack/storage-interface \
with methods:

- `get()` Retrieves the contents of a file. \
This method opens a file and return its contents, it throws an exception if file doesn't exist.
- `exists()` Checks if the file exists on the storage.
- `missing()` Checks if the file is missing from the storage.
- `save()` Saves the contents to the file. \
This method throws an exception if there are any troubles with saving a file (e.g. no space left on device).
- `delete()` Deletes one or more files. \
This method deletes one or many files and throws an exception if error occurs during deleting a file.

### Unit tests

Run tests using a command:

```
phpdbg -qrr ./vendor/bin/unit-tests
```

### Docker

```shell
$ docker-compose up -d
$ docker exec -w /var/www/html -it quillstack_local-storage sh
```

# Quillstack Local Storage

[![Tests](https://github.com/quillstack/local-storage/actions/workflows/tests.yml/badge.svg)](https://github.com/quillstack/local-storage/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/quillstack/local-storage.svg)](https://packagist.org/packages/quillstack/local-storage)
[![Downloads](https://img.shields.io/packagist/dt/quillstack/local-storage.svg)](https://packagist.org/packages/quillstack/local-storage)
[![PHP Version](https://img.shields.io/packagist/php-v/quillstack/local-storage)](https://packagist.org/packages/quillstack/local-storage)
[![StyleCI](https://github.styleci.io/repos/394779968/shield?branch=main)](https://github.styleci.io/repos/394779968?branch=main)
[![CodeFactor](https://www.codefactor.io/repository/github/quillstack/local-storage/badge)](https://www.codefactor.io/repository/github/quillstack/local-storage)
[![Quality Gate](https://sonarcloud.io/api/project_badges/measure?project=quillstack_local-storage&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=quillstack_local-storage)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=quillstack_local-storage&metric=coverage)](https://sonarcloud.io/summary/new_code?id=quillstack_local-storage)
[![Maintainability](https://sonarcloud.io/api/project_badges/measure?project=quillstack_local-storage&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_local-storage)
[![Reliability](https://sonarcloud.io/api/project_badges/measure?project=quillstack_local-storage&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_local-storage)
[![Security](https://sonarcloud.io/api/project_badges/measure?project=quillstack_local-storage&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=quillstack_local-storage)
[![Maintainability](https://api.codeclimate.com/v1/badges/d3fbd09f1580534b0c0e/maintainability)](https://codeclimate.com/github/quillstack/local-storage/maintainability)
[![License](https://img.shields.io/packagist/l/quillstack/local-storage)](https://github.com/quillstack/local-storage/blob/main/LICENSE)

The package to manage files on the local storage.

## Why this exists

It is the implementation behind [quillstack/storage-interface](https://github.com/quillstack/storage-interface),
and its whole job is to make an interface out of four PHP functions so that everything above it
can be tested without a disk.

The [cache](https://github.com/quillstack/cache), the [logger](https://github.com/quillstack/logger)
and [dotenv](https://github.com/quillstack/dotenv) all read and write through that interface. In
a test they are handed something that keeps files in an array; in production they are handed
this. Neither knows the difference, and neither calls `file_put_contents` itself.

**Nothing here does anything clever.** Reading it should take a minute and finding a bug in it
should be hard, which is the point of a package this small.

## Requirements

- PHP 8.1 or newer

## Installation

To install this package, run the standard command using _Composer_:

```
composer require quillstack/local-storage
```

## Usage

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

## Benchmark

Measured with [quillstack/benchmark](https://github.com/quillstack/benchmark) on a thousand
write-and-read pairs of a small file. Runs are interleaved and unconcurrent, each figure is the
median of five, and PHP is 8.5.7.

| | Version |
| --- | --- |
| quillstack/local-storage | 0.6.0 |
| league/flysystem | 3.35.3 |

| | Per write and read | Relative |
| --- | --- | --- |
| **quillstack/local-storage** | **83.6 µs** | — |
| league/flysystem | 85.7 µs | 1.02× |

**These are the same number.** Both call the same kernel, and two per cent between them is the
filesystem rather than either library. A benchmark of two thin wrappers over `file_put_contents`
was never going to say anything else, and it is here because the alternative is leaving a reader
to wonder.

The difference is what they reach. `league/flysystem` writes to S3, FTP, SFTP, Azure, Google
Cloud, in-memory and a dozen more, with stream support, visibility, MIME detection and directory
listings. This writes to the disk it is running on. **Where you need one of those, use
Flysystem** — and where you need a `StorageInterface` implementation that does not bring an
ecosystem with it, that is what this is.

## Tests

Run tests using a command:

```
phpdbg -qrr ./vendor/bin/unit-tests
```

## The rest of Quillstack

This is one component of [Quillstack](https://github.com/quillstack), a PHP framework which is
as simple to use as it is strict about what it does.

- [quillstack/storage-interface](https://github.com/quillstack/storage-interface) — what this implements
- [quillstack/cache](https://github.com/quillstack/cache) — which writes entries through it
- [quillstack/logger](https://github.com/quillstack/logger) — which writes entries through it too
- [quillstack/dotenv](https://github.com/quillstack/dotenv) — which reads a file through it

## License

MIT — see [LICENSE](https://github.com/quillstack/local-storage/blob/main/LICENSE).

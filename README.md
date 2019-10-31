[![Latest Stable Version](https://img.shields.io/packagist/v/drupol/composer-packages.svg?style=flat-square)](https://packagist.org/packages/drupol/composer-packages)
 [![GitHub stars](https://img.shields.io/github/stars/drupol/composer-packages.svg?style=flat-square)](https://packagist.org/packages/drupol/composer-packages)
 [![Total Downloads](https://img.shields.io/packagist/dt/drupol/composer-packages.svg?style=flat-square)](https://packagist.org/packages/drupol/composer-packages)
 [![Build Status](https://img.shields.io/travis/drupol/composer-packages/master.svg?style=flat-square)](https://travis-ci.org/drupol/composer-packages)
 [![Scrutinizer code quality](https://img.shields.io/scrutinizer/quality/g/drupol/composer-packages/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/composer-packages/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/drupol/composer-packages/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/composer-packages/?branch=master)
 [![Mutation testing badge](https://badge.stryker-mutator.io/github.com/drupol/composer-packages/master)](https://stryker-mutator.github.io)
 [![License](https://img.shields.io/packagist/l/drupol/composer-packages.svg?style=flat-square)](https://packagist.org/packages/drupol/composer-packages)
 [![Say Thanks!](https://img.shields.io/badge/Say-thanks-brightgreen.svg?style=flat-square)](https://saythanks.io/to/drupol)
 [![Donate!](https://img.shields.io/badge/Donate-Paypal-brightgreen.svg?style=flat-square)](https://paypal.me/drupol)

# Composer Packages

## Description

Composer Packages is a Composer plugin for getting information about installed packages in your project.

It could be very useful for anyone who wants to build a package discovery system, crawling the filesystem is then not
needed.

## Documentation

This package provides:

* An easy way to get information about installed packages,
* An easy way to retrieve packages that has a particular types,
* An easy way to find the installation directory of a package.
* An easy way to get any package version.
* An easy way to get any package dependencies.

### How does it work ?

When doing a `composer update` or `composer install`, the plugin will generate classes that are going to be
automatically loaded by the Composer autoload system.

Those classes contains statical information about packages that are installed in your project.
Among those static data, it also contains some useful methods. The number of methods in those classes can very depending
on the number of packages that are in your project.

This package idea has been inspired by the package [ocramius/package-versions](https://github.com/Ocramius/PackageVersions)
from the amazing [Marco Pivetta](https://github.com/Ocramius).

## Requirements

* PHP >= 7.1.3

## Installation

```composer require drupol/composer-packages --dev```

## Usage

### Composer command

#### Get the dependencies

```shell
$ composer dependencies [-r|--recursive]
Please type the package name:
twig/twig

.
│
└── twig/twig: v2.12.1
    ├── symfony/polyfill-ctype: v1.12.0
    └── symfony/polyfill-mbstring: v1.12.0
```

#### Get the parent dependencies

```shell
$ composer reverse-dependencies
Please type the package name:
twig/twig
.
│
└── drupol/php-conventions
    └── twig/twig
```

#### Get the dependencies of a type

```shell
$ composer packages-type
Please provide type of packages:
composer-plugin

ocramius/package-versions
phpro/grumphp
phpstan/extension-installer
```

### To get packages of a particular type

```php
<?php

declare(strict_types=1);

include './vendor/autoload.php';

use ComposerPackages\Types;

// Use your IDE auto completion to list all the available methods based on your installed packages.
$packages = Types::library();

foreach ($packages as $package) {
    $package->getName(); // $package is an instance of Composer\Package\PackageInterface
}

// You can also get an array
$packagesArray = iterator_to_array($packages);
```

### To get a package

```php
<?php

declare(strict_types=1);

include './vendor/autoload.php';

use ComposerPackages\Packages;
use Composer\Package\PackageInterface;

// Use your IDE auto completion to list all the available methods based on your installed packages.
$package = Packages::symfonyProcess();

// Package is an instance of Composer\Package\PackageInterface then:
$package->getName(); // To get the name.

// Find all the packages where the name starts with the letter "c".
$finder = static function (PackageInterface $package) : bool {
    return 'c' === str_split($package->getName())[0];
};

foreach (Packages::find($finder) as $package) {
    // Do something here.
}
```

### To get an installation directory

```php
<?php

declare(strict_types=1);

include './vendor/autoload.php';

use ComposerPackages\Directories;

// Use your IDE auto completion to list all the available methods based on your installed packages.
$directory = Directories::symfonyProcess();
```

### To get a package version

```php
<?php

declare(strict_types=1);

include './vendor/autoload.php';

use ComposerPackages\Versions;

// Use your IDE auto completion to list all the available methods based on your installed packages.
$version = Versions::symfonyProcess();
```

### To get a package dependencies

```php
<?php

declare(strict_types=1);

include './vendor/autoload.php';

use ComposerPackages\Dependencies;

// Use your IDE auto completion to list all the available methods based on your installed packages.
$dependencies = Dependencies::symfonyDependencyInjection();

foreach ($dependencies as $dependency) {
    echo $dependency; // $dependency is string, the package name.
}

// You can also get an array
$dependenciesArray = iterator_to_array($dependencies);
```


**Note:** If composer is not already installed, you might get an error like
below when using this package:

```
In Types.php line […]:

  Attempted to load class "ArrayLoader" from namespace "Composer\Package\Loader".
  Did you forget a "use" statement for e.g. "…\ArrayLoader", "…\ArrayLoader" or "…\ArrayLoader"?
```

If you do, you can explicitly require composer in your project, to ensure it's
available:

```
composer require composer/composer
```

### To get a package version

```php
<?php

declare(strict_types=1);

include './vendor/autoload.php';

use ComposerPackages\Versions;

// Use your IDE auto completion to list all the available methods based on your installed packages.
$version = Versions::symfonyProcess();
```

## Code quality and tests

Every time changes are introduced into the library, [Travis CI](https://travis-ci.org/drupol/composer-packages/builds)
run the tests and the benchmarks.

The library has tests written with [PHPUnit](http://www.phpunit.de/).

Before each commit some inspections are executed with [GrumPHP](https://github.com/phpro/grumphp),
run `./vendor/bin/grumphp run` to trigger them manually.

[PHPInfection](https://github.com/infection/infection) is used to ensure that your code is properly tested,
run `composer infection` to test your code.

## Contributing

Feel free to contribute to this library by sending Github pull requests. I'm quite reactive :-)

# bitexpert/phpstan-magento

This package provides some additional features for PHPStan to make it work for Magento 2 projects.
You can use this PHPStan extension for both Magento module projects and Magento application projects.

[![Build Status](https://github.com/bitExpert/phpstan-magento/workflows/ci/badge.svg?branch=master)](https://github.com/bitExpert/phpstan-magento/actions)
[![Coverage Status](https://coveralls.io/repos/github/bitExpert/phpstan-magento/badge.svg?branch=master)](https://coveralls.io/github/bitExpert/phpstan-magento?branch=master)
[![installs on Packagist](https://img.shields.io/packagist/dt/bitExpert/phpstan-magento)](https://packagist.org/packages/bitExpert/phpstan-magento/)

## Requirements

PHP: PHP 7.2 or higher

Magento: Magento 2.3.0 or higher

PHPStan: PHPStan 1.4

If you are using a Magento version that requires an older version of PHPStan (e.g. 0.12.77),  you need to manually upgrade it before 
installing this extension. in your composer.json Change the PHPStan version to `~1.4` and run:

```
composer update phpstan/phpstan --with-all-dependencies
```

## Installation

The preferred way of installing `bitexpert/phpstan-magento` is through Composer.
You can add `bitexpert/phpstan-magento` as a dev dependency, as follows:

```
composer.phar require --dev bitexpert/phpstan-magento
```

### PHPStan configuration

If you have not already a PHPStan configuration file `phpstan.neon` in your project, create a new empty file next to your `composer.json` file.
 
This PHPStan extension needs to be registered with PHPStan so that PHPStan can load it properly.
The easiest way to do this is to install the `phpstan/extension-installer` package as follows:

```
composer.phar require --dev phpstan/extension-installer
```

If you're using composer >= 2.2.0 you have to allow the execution of composer plugins ([see allow-plugins section](https://getcomposer.org/doc/06-config.md#allow-plugins)) as follows:

```
  - Installing phpstan/extension-installer (1.1.0): Extracting archive
phpstan/extension-installer contains a Composer plugin which is currently not in your allow-plugins config. See https://getcomposer.org/allow-plugins
Do you trust "phpstan/extension-installer" to execute code and wish to enable it now? (writes "allow-plugins" to composer.json) [y,n,d,?] y
```

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include extension.neon in your project's PHPStan config:

```neon
includes:
	- vendor/bitexpert/phpstan-magento/extension.neon
```
</details>

If you are using phpstan-magento 0.15.0 and earlier, you need to register the custom autoloader that comes with this extension by adding `vendor/bitexpert/phpstan-magento/autoload.php`
as a bootstrap file. If you are using phpstan-magento > 0.15.0 this step is done automatically by the extension itself.

```neon
parameters:
	bootstrapFiles:
		- vendor/bitexpert/phpstan-magento/autoload.php
```

## Feature overview

This PHPStan extension works for both Magento module projects and Magento application projects.

- Class generator for factory & proxy classes
- Mocked classes autoloader
- TestFramework autoloader
- Type hints
  - TestFramework ObjectManager type hints
  - ObjectManager type hints
- Magic method calls
- Extension attributes
- PHPStan rules
  - Service contracts
  - Collections should be used directly via factory
  
For a detailed overview, check the feature documentation [here](docs/features.md).

## Contribute

Please feel free to fork and extend existing or add new features and send a pull request with your changes! To establish
a consistent code quality, please provide unit tests for all your changes and adapt the documentation.

## Want To Contribute?

If you feel that you have something to share, then we’d love to have you.
Check out [the contributing guide](CONTRIBUTING.md) to find out how, as well as what we expect from you.

## License

PHPStan Magento Extension is released under the MIT License.

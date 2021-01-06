# bitexpert/phpstan-magento

This package provides some additional features for PHPStan to make it
work for Magento 2 projects.

[![Build Status](https://travis-ci.org/bitExpert/phpstan-magento.svg?branch=master)](https://travis-ci.org/bitExpert/phpstan-magento)
[![Coverage Status](https://coveralls.io/repos/github/bitExpert/phpstan-magento/badge.svg?branch=master)](https://coveralls.io/github/bitExpert/phpstan-magento?branch=master)

## Installation

The preferred way of installing `bitexpert/phpstan-magento` is through Composer.
You can add `bitexpert/phpstan-magento` as a dev dependency, as follows:

```
composer.phar require --dev bitexpert/phpstan-magento
```

Include extension.neon and the autoloader.php file in your project's PHPStan config:

```neon
parameters:
	bootstrapFiles:
		- vendor/bitexpert/phpstan-magento/autoload.php
includes:
	- vendor/bitexpert/phpstan-magento/extension.neon
```

⚠️ When you use a version of `phpstan/phpstan` lower than 0.12.26 you should replace `bootstrapFiles` with `autoload_files` in the configuration mentioned above.

## Features

### Class generator for factory & proxy classes
The extension adds a class generator for factory & proxy classes similar as Magento does it. When running PHPStan in 
context of a Magento application this is not needed if you point PHPStan also to the generated files folder. When running 
Magento in the context of a module, this is required so that PHPStan gets the full picture of all classes needed.

### Mocked classes autoloader
The extension adds an autoloader for "mocked" classes. These are classes that replace the Magento specific implementations 
to fix problems with type hints or missing methods in interfaces and such. The autoloader will check if a class, interface, 
or trait exists locally in the extension's folder of mocks. If so, it will load the local version instead of the one being 
shipped by Magento. Once those problems are fixed in Magento, those mocks can be removed again.

### TestFramework autoloader
The extension provides an autoloader for `Magento\TestFramework` classes to let you run PHPStan also against your test classes.

#### TestFramework ObjectManager type hints
A type extension was added so that `Magento\Framework\TestFramework\Unit\Helper\ObjectManager` calls return the correct return type. 
Additionally, a PHPStan rule was added to check that only `Magento\Framework\Data\Collection` sub classes can be passed to  
`Magento\Framework\TestFramework\Unit\Helper\ObjectManager::getCollectionMock()`.

### ObjectManager type hints
A type extension was added so that `Magento\Framework\App\ObjectManager` calls return the correct return type.

### Support for magic method calls
For some classes like the `Magento\Framework\DataObject` or `Magento\Framework\Session\SessionManager` logic was added 
to be able to support magic method calls.

### PHPStan rules

The following rules are available to run checks against your codebase, e.g. if your implementation adheres to the 
service contracts specification. Each of the rules can be disabled if needed.

#### Service contracts

Since Magento framework version 100.1.0 entities must not be responsible for their own loading, service contracts should
be used to persist entities.

To disable this rule add the following code to your `phpstan.neon` configuration file:
```
parameters:
    magento:
        checkServiceContracts: false
```

#### Collections should be used directly via factory

Since Magento framework version 101.0.0 Collections should be used directly via factory instead of calling 
`\Magento\Framework\Model\AbstractModel::getCollection()` directly.

To disable this rule add the following code to your `phpstan.neon` configuration file:
```
parameters:
    magento:
        checkCollectionViaFactory: false
```

## Contribute

Please feel free to fork and extend existing or add new features and send a pull request with your changes! To establish
a consistent code quality, please provide unit tests for all your changes and adapt the documentation.

## Want To Contribute?

If you feel that you have something to share, then we’d love to have you.
Check out [the contributing guide](CONTRIBUTING.md) to find out how, as well as what we expect from you.

## License

PHPStan Magento Extension is released under the MIT License.

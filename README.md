# bitexpert/phpstan-magento

This package provides some additional features for PHPStan to make it
work for Magento 2 projects.

## Installation

The preferred way of installing `bitexpert/phpstan-magento` is through Composer.
You can add `bitexpert/phpstan-magento` as a dev dependency, as follows:

```
composer.phar require --dev bitexpert/phpstan-magento
```

Include extension.neon in your project's PHPStan config:

```
includes:
	- vendor/bitexpert/phpstan-magento/extension.neon
```

## Features
1. The extension adds an class generator for factory & proxy classes similar as Magento does it. When running PHPStan in context of a Magento application this is not needed if you point PHPStan also the the generated files folder. When running Magento in a context of a module, this is required so that PHPStan get's the full picture of all classes needed.
2. The extension adds an autoloader for "mocked" classes. These are classes that replace the Magento specific implementations to fix problems with type hints or missing methods in interfaces and such. The autoloader will check if a class, interface or trait exists locally in the extension. If so, it will load the local version instead of the one being shipped by Magento. Once those problems are fixed in Magento, those mocks can be removed again.
3. A type extension was added so that `ObjectManager` calls return the correct return type.
4. For some classes like the `DataObject` or the `SessionManager` logic was added to be able to support magic method calls.

## Known Issues

Below is a list of known issues when using this extension:

### PHPStan shim does not generate factories, proxies, etc.

This is because the PHPStan shim is included as a phar archive and does therefore not support overriding certain methods in it's namespace. The current known fix for this is to manually load the autoloader that comes with this extension.

Include the autoload.php file in the `autoload_files`-section of your `phpstan.neon`:

```neon
parameters:
    autoload_files:
        - vendor/bitexpert/phpstan-magento/autoload.php
```

## Contribute

Please feel free to fork and extend existing or add new features and send a pull request with your changes! To establish a consistent code quality, please provide unit tests for all your changes and adapt the documentation.

## Want To Contribute?

If you feel that you have something to share, then we’d love to have you.
Check out [the contributing guide](CONTRIBUTING.md) to find out how, as well as what we expect from you.

## License

PHPStan Magento Extension is released under the MIT License.

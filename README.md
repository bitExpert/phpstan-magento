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

## Contribute

Please feel free to fork and extend existing or add new features and send a pull request with your changes! To establish a consistent code quality, please provide unit tests for all your changes and adapt the documentation.

## Want To Contribute?

If you feel that you have something to share, then we’d love to have you.
Check out [the contributing guide](CONTRIBUTING.md) to find out how, as well as what we expect from you.

## License

PHPStan Magento Extension is released under the MIT License.

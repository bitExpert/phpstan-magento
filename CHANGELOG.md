# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.27.0

### Added

- Nothing.

### Deprecated

- [#279](https://github.com/bitExpert/phpstan-magento/pull/279) Update phpstan/phpstan requirement to ~1.9.2

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.26.0

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#276](https://github.com/bitExpert/phpstan-magento/pull/276) Check existing extension interface for types

## 0.25.0

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#275](https://github.com/bitExpert/phpstan-magento/pull/275) Restructure the docs
- [#274](https://github.com/bitExpert/phpstan-magento/pull/274) Don't use ?array type hint in ext attributes
- [#273](https://github.com/bitExpert/phpstan-magento/pull/273) Make code compatible with PHP 7.2

## 0.24.0

### Added

- [#269](https://github.com/bitExpert/phpstan-magento/pull/269) Add Magento 2.4.5 to CI pipeline
- [#267](https://github.com/bitExpert/phpstan-magento/pull/267) Use Magento root for TestFrameworkAutoloader

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#268](https://github.com/bitExpert/phpstan-magento/pull/268) Autoloaders prefer local classes
- [#261](https://github.com/bitExpert/phpstan-magento/pull/261) Update phpstan/phpstan requirement from ~1.7.2 to ~1.8.2

## 0.23.1

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#266](https://github.com/bitExpert/phpstan-magento/pull/266) Allow arrays in extension attributes
- [#264](https://github.com/bitExpert/phpstan-magento/pull/264) Fix factory generation for "FactoryThing" classes

## 0.23.0

### Added

- [#243](https://github.com/bitExpert/phpstan-magento/pull/243) Upgrade to PHPStan 1.7

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.22.0

### Added

- [#246](https://github.com/bitExpert/phpstan-magento/pull/246) Do not yet require PHPStan 1.7
- [#242](https://github.com/bitExpert/phpstan-magento/pull/242) Add link to related blog post
- [#239](https://github.com/bitExpert/phpstan-magento/pull/239) Update madewithlove/license-checker requirement from to ^1.2

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.21.0

### Added

- [#241](https://github.com/bitExpert/phpstan-magento/pull/241) PHPStan 1.6 compatibility

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.20.0

### Added

- [#238](https://github.com/bitExpert/phpstan-magento/pull/238) Bump phpstan/phpstan to 1.5.7
- [#232](https://github.com/bitExpert/phpstan-magento/pull/232) Bump league/commonmark to 2.3.0
- [#231](https://github.com/bitExpert/phpstan-magento/pull/231) Support for 2.4.4
- [#230](https://github.com/bitExpert/phpstan-magento/pull/230) Bump phpunit/phpunit to 9.5.20

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.19.0

### Added

- Nothing.

### Deprecated

- [#223](https://github.com/bitExpert/phpstan-magento/pull/223) Deprecate PHPStan 1.4 and make extension compatible with PHPStan 1.5

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.18.0

### Added

- [#221](https://github.com/bitExpert/phpstan-magento/pull/221) Feature/packagist downloads
- [#220](https://github.com/bitExpert/phpstan-magento/pull/220) Add documenation for composer plugins
- [#219](https://github.com/bitExpert/phpstan-magento/pull/219) Bump phpunit/phpunit to 9.5.19
- [#218](https://github.com/bitExpert/phpstan-magento/pull/218) Bump phpstan/phpstan to 1.4.10
- [#216](https://github.com/bitExpert/phpstan-magento/pull/216) Bump nette/neon to 3.3.3
- [#212](https://github.com/bitExpert/phpstan-magento/pull/212) Bump captainhook/captainhook to 5.10.8
- [#210](https://github.com/bitExpert/phpstan-magento/pull/210) Bump league/commonmark to 2.2.3
- [#201](https://github.com/bitExpert/phpstan-magento/pull/201) Bump captainhook/plugin-composer to 5.3.3
- [#202](https://github.com/bitExpert/phpstan-magento/pull/202) Bump symfony/finder to 5.4.3

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.17.0

### Added

- Nothing

### Deprecated

- [#192](https://github.com/bitExpert/phpstan-magento/pull/192) Deprecate PHPStan 1.3 and make extension compatible with PHPStan 1.4

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.16.0

### Added

- [#191](https://github.com/bitExpert/phpstan-magento/pull/191) Add autoloader for extension classes
- [#190](https://github.com/bitExpert/phpstan-magento/pull/190) Check if classes referenced in neon config exist
- [#188](https://github.com/bitExpert/phpstan-magento/pull/188) Automatically register the autoloader file

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.15.0

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- [#187](https://github.com/bitExpert/phpstan-magento/pull/187) Bump phpstan/phpstan to 1.3.3

### Fixed

- Nothing.

## 0.14.0

### Added

- [#186](https://github.com/bitExpert/phpstan-magento/pull/186) Bump captainhook/captainhook to 5.10.6
- [#181](https://github.com/bitExpert/phpstan-magento/pull/181) Bump league/commonmark to 2.1.1
- [#178](https://github.com/bitExpert/phpstan-magento/pull/178) Bump phpunit/phpunit to 9.5.11
- [#175](https://github.com/bitExpert/phpstan-magento/pull/175) Lint neon files in CI pipeline

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#179](https://github.com/bitExpert/phpstan-magento/pull/179) Rework extension attribute handling for standalone modules

## 0.13.0

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- [#173](https://github.com/bitExpert/phpstan-magento/pull/173) Upgrade to PHPStan 1.2, drop support for PHPStan 1.1

### Fixed

- Nothing.

## 0.12.0

### Added

- [#171](https://github.com/bitExpert/phpstan-magento/pull/171) Improve docs
- [#170](https://github.com/bitExpert/phpstan-magento/pull/170) Throw exception in autoloader
- [#169](https://github.com/bitExpert/phpstan-magento/pull/169) Bump captainhook/captainhook to 5.10.5
- [#168](https://github.com/bitExpert/phpstan-magento/pull/168) Bump squizlabs/php_codesniffer to 3.6.2
- [#164](https://github.com/bitExpert/phpstan-magento/pull/164) Bump nikic/php-parser to 4.13.2
- [#163](https://github.com/bitExpert/phpstan-magento/pull/163) Determine tmpDir path relative to config file when appropriate
- [#162](https://github.com/bitExpert/phpstan-magento/pull/162) Support for extension attributes
- [#159](https://github.com/bitExpert/phpstan-magento/pull/159) Bump phpstan/phpstan-strict-rules to 1.1.0
- [#158](https://github.com/bitExpert/phpstan-magento/pull/158) Bump phpstan/phpstan to 1.2.0

### Deprecated

- Nothing.

### Removed

- [#171](https://github.com/bitExpert/phpstan-magento/pull/171) Remove nette/neon dependency

### Fixed

- Nothing.

## 0.11.0

### Added

- [#157](https://github.com/bitExpert/phpstan-magento/pull/157) Allow PHP 8.0 as PHP runtime

### Deprecated

- [#156](https://github.com/bitExpert/phpstan-magento/pull/156) Drop support for PHPStan 0.12.x

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.10.0

### Added

- [#155](https://github.com/bitExpert/phpstan-magento/pull/155) Composer dependency upgrade
- [#154](https://github.com/bitExpert/phpstan-magento/pull/154) Bump nette/neon to 3.3.1
- [#153](https://github.com/bitExpert/phpstan-magento/pull/153) Bump nikic/php-parser to 4.13.1
- [#150](https://github.com/bitExpert/phpstan-magento/pull/150) Bump captainhook/plugin-composer to 5.3.2
- [#149](https://github.com/bitExpert/phpstan-magento/pull/149) Bump composer/composer to 2.1.9
- [#148](https://github.com/bitExpert/phpstan-magento/pull/148) Bump squizlabs/php_codesniffer to 3.6.1
- [#146](https://github.com/bitExpert/phpstan-magento/pull/146) Bump mikey179/vfsstream to 1.6.10
- [#145](https://github.com/bitExpert/phpstan-magento/pull/145) Bump phpunit/phpunit to 9.5.10
- [#141](https://github.com/bitExpert/phpstan-magento/pull/141) Bump phpstan/phpstan to 0.12.99
- [#140](https://github.com/bitExpert/phpstan-magento/pull/140) Bump captainhook/captainhook to 5.10.2
- [#135](https://github.com/bitExpert/phpstan-magento/pull/135) Bump phpstan/phpstan-strict-rules to 0.12.11
- [#134](https://github.com/bitExpert/phpstan-magento/pull/134) Bump phpstan/phpstan-phpunit to 0.12.22

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.9.0

### Added

- [#131](https://github.com/bitExpert/phpstan-magento/pull/131) Bump phpstan/phpstan to 0.12.94
- [#130](https://github.com/bitExpert/phpstan-magento/pull/130) Bump phpunit/phpunit to 9.5.8
- [#129](https://github.com/bitExpert/phpstan-magento/pull/129) Bump nikic/php-parser to 4.12.0
- [#126](https://github.com/bitExpert/phpstan-magento/pull/126) Bump mikey179/vfsstream to 1.6.9
- [#125](https://github.com/bitExpert/phpstan-magento/pull/125) Bump phpstan/phpstan-phpunit to 0.12.21
- [#123](https://github.com/bitExpert/phpstan-magento/pull/123) Bump phpstan/phpstan-strict-rules to 0.12.10
- [#119](https://github.com/bitExpert/phpstan-magento/pull/119) Add more unit tests
- [#118](https://github.com/bitExpert/phpstan-magento/pull/118) Turn magento/framework into dev dependency
- [#117](https://github.com/bitExpert/phpstan-magento/pull/117) Switch to Composer 2 for CI workflow

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#133](https://github.com/bitExpert/phpstan-magento/pull/133) Omit namespace for non-namespace Proxy
- [#132](https://github.com/bitExpert/phpstan-magento/pull/132) Omit namespace for non-namespace Factory

## 0.8.0

### Added

- [#116](https://github.com/bitExpert/phpstan-magento/pull/116) Add license checker to CI workflow
- [#115](https://github.com/bitExpert/phpstan-magento/pull/115) Upgrade Composer dependencies
- [#114](https://github.com/bitExpert/phpstan-magento/pull/114) Bump phpstan/phpstan-phpunit to 0.12.20
- [#111](https://github.com/bitExpert/phpstan-magento/pull/111) Bump phpstan/phpstan to 0.12.89
- [#110](https://github.com/bitExpert/phpstan-magento/pull/110) Bump phpunit/phpunit to 9.5.5
- [#109](https://github.com/bitExpert/phpstan-magento/pull/109) Bump captainhook/captainhook to 5.10.1
- [#108](https://github.com/bitExpert/phpstan-magento/pull/108) Bump captainhook/plugin-composer to 5.3.1
- [#100](https://github.com/bitExpert/phpstan-magento/pull/100) Bump nikic/php-parser to 4.10.5
- [#98](https://github.com/bitExpert/phpstan-magento/pull/98) Upgrade to GitHub-native Dependabot
- [#96](https://github.com/bitExpert/phpstan-magento/pull/96) Bump composer/composer to 2.0.13
- [#90](https://github.com/bitExpert/phpstan-magento/pull/90) Bump squizlabs/php_codesniffer to 3.6.0

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.7.0

### Added

- [#80](https://github.com/bitExpert/phpstan-magento/pull/80) Update Composer dependencies
- [#79](https://github.com/bitExpert/phpstan-magento/pull/79) Bump phpstan/phpstan from 0.12.80 to 0.12.81
- [#78](https://github.com/bitExpert/phpstan-magento/pull/78) Bump phpstan/phpstan-phpunit from 0.12.17 to 0.12.18
- [#77](https://github.com/bitExpert/phpstan-magento/pull/77) Bump captainhook/captainhook from 5.4.4 to 5.4.5
- [#76](https://github.com/bitExpert/phpstan-magento/pull/76) Bump captainhook/plugin-composer from 5.2.3 to 5.2.4
- [#74](https://github.com/bitExpert/phpstan-magento/pull/74) Bump nette/neon from 3.2.1 to 3.2.2
- [#67](https://github.com/bitExpert/phpstan-magento/pull/67) Add phpstan/phpstan-strict-rules 
- [#64](https://github.com/bitExpert/phpstan-magento/pull/64) PHPStan extensions run on max
- [#63](https://github.com/bitExpert/phpstan-magento/pull/63) Bump magento/framework from 103.0.1 to 103.0.2
- [#59](https://github.com/bitExpert/phpstan-magento/pull/59) Bump phpunit/phpunit from 9.5.1 to 9.5.2

### Deprecated

- Nothing.

### Removed

- [#60](https://github.com/bitExpert/phpstan-magento/pull/60) Replace Phing with Composer scripts

### Fixed

- [#70](https://github.com/bitExpert/phpstan-magento/pull/70) Install matching lowest version of strict rules

## 0.6.0

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#52](https://github.com/bitExpert/phpstan-magento/pull/52) Fix params for DataObject get & set

## 0.5.0

### Added

- [#47](https://github.com/bitExpert/phpstan-magento/pull/47) Add type for ObjectManager helper for tests
- [#48](https://github.com/bitExpert/phpstan-magento/pull/48) Fix proxy generation

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.4.1

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#41](https://github.com/bitExpert/phpstan-magento/pull/41) Removes upper version constraint of phpstan

## 0.4.0

### Added

- [#40](https://github.com/bitExpert/phpstan-magento/pull/40) Use same constraint as latest Magento release
- [#39](https://github.com/bitExpert/phpstan-magento/pull/39) Apply phpstan.rules.rule tag dynamically via DI
- [#36](https://github.com/bitExpert/phpstan-magento/pull/36) Add deprecation rules for AbstractModel classe

### Deprecated

- [#40](https://github.com/bitExpert/phpstan-magento/pull/40) Deprecated Config Option autoload_files

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.3.0

### Added

- [#35](https://github.com/bitExpert/phpstan-magento/pull/35) Make DataObject extension stricter

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.2.0

### Added

- [#33](https://github.com/bitExpert/phpstan-magento/pull/33) Upgrade PHPStan dependency to 0.12.24

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.1

### Added

- [#32](https://github.com/bitExpert/phpstan-magento/pull/32) Add autoloader for Magento\TestFramework classes

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.0

### Added

- [#29](https://github.com/bitExpert/phpstan-magento/pull/29) Improve code generation for factories & proxies

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

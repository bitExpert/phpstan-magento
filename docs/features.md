# Features

## Class generator for factory & proxy classes
This PHPStan extension includes a class generator and autoloader for factory & proxy classes similar to what Magento does. When running PHPStan in
the context of a Magento application this is not needed if you point PHPStan also to the generated files folder. When running
Magento in the context of a stand-alone module the autoloader helps PHPStan to properly resolve the factory & proxy classes.

## Mocked classes autoloader
This PHPStan extension provides an autoloader for "mocked" classes. These are classes that replace the Magento-specific implementations
to fix problems with type hints or missing methods in interfaces and such. The autoloader will check if a class, interface,
or trait exists locally in the extension's folder of mocks. If so, it will load the local version instead of the one being
shipped by Magento. Once those problems are fixed in Magento, those mocked files can be removed again.

## TestFramework autoloader
This PHPStan extension provides an autoloader for `Magento\TestFramework` classes to let you run PHPStan also against your test classes.

## Type hints

### TestFramework ObjectManager type hints
A type extension is provided for `Magento\Framework\TestFramework\Unit\Helper\ObjectManager` method calls to return the correct return type.
Additionally, a PHPStan rule checks that only `Magento\Framework\Data\Collection` sub classes can be passed to  
`Magento\Framework\TestFramework\Unit\Helper\ObjectManager::getCollectionMock()`.

### ObjectManager type hints
A type extension is provided so that `Magento\Framework\App\ObjectManager` method calls do return the correct return type.

## Magic method calls
For some classes like `Magento\Framework\DataObject` or `Magento\Framework\Session\SessionManager` PHPStan logic is provided
to be able to let the magic method calls return proper types.

## Extension attributes
This PHPStan extension supports [extension attributes](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/extension_attributes/adding-attributes.html) by parsing the `extension_attributes.xml` files.

By default, all `extension_attributes.xml` found recursively in the current working directory will be taken into account.
Current working directory means the directory in which your `phpstan.neon` file resides. If you need to change this behavior,
you can define a custom path by pointing the `magentoRoot` parameter to a different directory.

To disable this rule add the following code to your `phpstan.neon` configuration file:
```neon
parameters:
    magento:
        magentoRoot: /tmp/my/other/dir
```

Currently, all Magento modules found in your project are taken into account. Disabled modules are not yet ignored!

## PHPStan rules

The following rules are available to run checks against your codebase, e.g. if your implementation adheres to the
service contracts specification. Each of the rules can be disabled if needed.

### Service contracts

Since Magento framework version 100.1.0 entities must not be responsible for their own loading, [service contracts](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/service-contracts/service-contracts.html) should
be used to persist entities.

To disable this rule add the following code to your `phpstan.neon` configuration file:
```neon
parameters:
    magento:
        checkServiceContracts: false
```

### Collections should be used directly via factory

Since Magento framework version 101.0.0 Collections should be used directly via factory instead of calling
`\Magento\Framework\Model\AbstractModel::getCollection()` directly.

To disable this rule add the following code to your `phpstan.neon` configuration file:
```neon
parameters:
    magento:
        checkCollectionViaFactory: false
```

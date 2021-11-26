<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use bitExpert\PHPStan\Magento\Autoload\Cache\FileCacheStorage;
use bitExpert\PHPStan\Magento\Autoload\FactoryAutoloader;
use bitExpert\PHPStan\Magento\Autoload\MockAutoloader;
use bitExpert\PHPStan\Magento\Autoload\ProxyAutoloader;
use bitExpert\PHPStan\Magento\Autoload\TestFrameworkAutoloader;
use Nette\Neon\Neon;
use PHPStan\Cache\Cache;

// This autoloader implementation supersedes the former \bitExpert\PHPStan\Magento\Autoload\Autoload implementation
(function (array $argv = []) {
    // Sadly we don't have access to the parsed phpstan.neon configuration at this point we need to look up the
    // location of the config file and parse it with the Neon parser to be able to extract the tmpDir definition!
    $configFile = '';
    if (count($argv) > 0) {
        foreach($argv as $idx => $value) {
            if ((strtolower($value) === '-c') && isset($argv[$idx + 1])) {
                $configFile = $argv[$idx + 1];
                break;
            }
        }
    }

    if (empty($configFile)) {
        $currentWorkingDirectory = getcwd();
        foreach (['phpstan.neon', 'phpstan.neon.dist'] as $discoverableConfigName) {
            $discoverableConfigFile = $currentWorkingDirectory . DIRECTORY_SEPARATOR . $discoverableConfigName;
            if (file_exists($discoverableConfigFile) && is_readable(($discoverableConfigFile))) {
                $configFile = $discoverableConfigFile;
                break;
            }
        }
    }

    $tmpDir = sys_get_temp_dir() . '/phpstan';
    if (!empty($configFile)) {
        $neonConfig = Neon::decode(file_get_contents($configFile));
        if(is_array($neonConfig) && isset($neonConfig['parameters']) && isset($neonConfig['parameters']['tmpDir'])) {
            $tmpDir = $neonConfig['parameters']['tmpDir'];
        }
    }

    $cache = new Cache(new FileCacheStorage($tmpDir . '/cache/PHPStan'));

    $mockAutoloader = new MockAutoloader();
    $testFrameworkAutoloader = new TestFrameworkAutoloader();
    $factoryAutoloader = new FactoryAutoloader($cache);
    $proxyAutoloader = new ProxyAutoloader($cache);
    $extensionInterfacAutoloader = \bitExpert\PHPStan\Magento\Autoload\ExtensionInterfaceAutoloader::create($cache);

    \spl_autoload_register([$mockAutoloader, 'autoload'], true, true);
    \spl_autoload_register([$testFrameworkAutoloader, 'autoload'], true, false);
    \spl_autoload_register([$factoryAutoloader, 'autoload'], true, false);
    \spl_autoload_register([$proxyAutoloader, 'autoload'], true, false);
    \spl_autoload_register([$extensionInterfacAutoloader, 'autoload'], true, false);
})($GLOBALS['argv'] ?? []);

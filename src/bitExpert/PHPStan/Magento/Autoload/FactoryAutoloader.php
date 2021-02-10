<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace bitExpert\PHPStan\Magento\Autoload;

use PHPStan\Cache\Cache;

class FactoryAutoloader
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * FactoryAutoloader constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function autoload(string $class): void
    {
        if (!preg_match('#Factory$#', $class)) {
            return;
        }

        $cacheFilename = $this->cache->load($class, '');
        if ($cacheFilename === null) {
            $this->cache->save($class, '', $this->getFileContents($class));
            $cacheFilename = $this->cache->load($class, '');
        }

        require_once($cacheFilename);
    }

    /**
     * Generate the factory file content as Magento would.
     *
     * @param string $class
     * @return string
     */
    protected function getFileContents(string $class): string
    {
        $namespace = explode('\\', ltrim($class, '\\'));
        $factoryClassname = array_pop($namespace);
        $originalClassname = str_replace('Factory', '', $factoryClassname);
        $namespace = implode('\\', $namespace);

        $template = [
            '<?php',
            'namespace {NAMESPACE};',
            '',
            '/**',
            ' * Factory class for @see \\{NAMESPACE}\\{CLASSNAME}',
            ' */',
            'class {FACTORY_CLASSNAME}',
            '{',
            '    /**',
            '     * Create class instance with specified parameters',
            '     *',
            '     * @param array $data',
            '     * @return {CLASSNAME}',
            '     */',
            '    public function create(array $data = array()) {}',
            '}',
            '',
        ];

        return str_replace(
            [
                '{NAMESPACE}',
                '{CLASSNAME}',
                '{FACTORY_CLASSNAME}'
            ],
            [
                $namespace,
                $originalClassname,
                $factoryClassname
            ],
            implode("\n", $template)
        );
    }
}

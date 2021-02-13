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

namespace bitExpert\PHPStan\Magento\Autoload\Cache;

use PHPStan\Cache\CacheStorage;

class FileCacheStorage implements CacheStorage
{
    /**
     * @var string
     */
    private $directory;

    /**
     * FileCacheStorage constructor.
     *
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param string $key
     * @param string $variableKey
     * @return mixed
     */
    public function load(string $key, string $variableKey)
    {
        return (function (string $key): ?string {
            $cacheDir = $this->getCacheDir($key);
            $cacheFile = $this->getCacheFile($key);
            if (!is_file($cacheDir . '/' . $cacheFile)) {
                return null;
            }

            return $cacheDir . '/' . $cacheFile;
        })($key);
    }

    /**
     * @param string $key
     * @param string $variableKey
     * @param mixed $data
     */
    public function save(string $key, string $variableKey, $data): void
    {
        $cacheDir = $this->getCacheDir($key);
        $cacheFile = $this->getCacheFile($key);
        $this->makeDir($cacheDir);
        $tmpSuccess = @file_put_contents($cacheDir . '/' . $cacheFile, $data);
        if ($tmpSuccess === false) {
            throw new \InvalidArgumentException(
                sprintf('Could not write data to cache file %s.', $cacheDir . '/' . $cacheFile)
            );
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function getCacheDir(string $key): string
    {
        $keyHash = sha1($key);
        $firstDirectory = sprintf('%s/%s', $this->directory, substr($keyHash, 0, 2));
        return sprintf('%s/%s', $firstDirectory, substr($keyHash, 2, 2));
    }

    /**
     * @param string $key
     * @return string
     */
    private function getCacheFile(string $key): string
    {
        return sprintf('%s.php', sha1($key));
    }

    /**
     * @param string $directory
     */
    private function makeDir(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        $result = @mkdir($directory, 0777, true);
        if ($result === \false) {
            clearstatcache();
            if (is_dir($directory)) {
                return;
            }

            $error = error_get_last();
            throw new \InvalidArgumentException(
                sprintf(
                    'Failed to create directory "%s" (%s).',
                    $this->directory,
                    $error !== null ? $error['message'] : 'unknown cause'
                )
            );
        }
    }
}

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

/**
 * Stores the source code the autoloaders generate and hands back the path of the file holding it.
 *
 * This deliberately is not a PHPStan\Cache\CacheStorage: the autoloaders need the path of the
 * generated file to require it, while a CacheStorage has to return what was handed to save().
 * Mixing the two made the autoloaders require the source code itself once PHPStan started to
 * serve cache entries from its shared memory arena.
 */
class GeneratedFileCache
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $magentoRoot;

    /**
     * GeneratedFileCache constructor.
     *
     * @param string $directory
     * @param string $magentoRoot
     */
    public function __construct(string $directory, string $magentoRoot)
    {
        $this->directory = $directory;
        $this->magentoRoot = $magentoRoot;
    }

    /**
     * Returns the path of the file generated for the given key, null when nothing was generated yet.
     *
     * @param string $key
     * @return string|null
     */
    public function getFile(string $key): ?string
    {
        $cacheFile = $this->getCacheDir($key) . '/' . $this->getCacheFile($key);
        if (!is_file($cacheFile)) {
            return null;
        }

        return $cacheFile;
    }

    /**
     * Writes the given source code and returns the path of the file holding it.
     *
     * @param string $key
     * @param string $contents
     * @return string
     */
    public function putFile(string $key, string $contents): string
    {
        $cacheDir = $this->getCacheDir($key);
        $cacheFile = $cacheDir . '/' . $this->getCacheFile($key);
        $this->makeDir($cacheDir);
        $written = @file_put_contents($cacheFile, $contents);
        if ($written === false) {
            throw new \InvalidArgumentException(
                sprintf('Could not write data to cache file %s.', $cacheFile)
            );
        }

        return $cacheFile;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getCacheDir(string $key): string
    {
        $keyHash = sha1(sprintf('%s/%s', $this->magentoRoot, $key));
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

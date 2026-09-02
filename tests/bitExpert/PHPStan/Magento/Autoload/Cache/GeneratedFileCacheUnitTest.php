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

use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class GeneratedFileCacheUnitTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;
    /**
     * @var GeneratedFileCache
     */
    private $cache;

    public function setUp(): void
    {
        $this->root = vfsStream::setup('tmp');
        $this->cache = new GeneratedFileCache($this->root->url(), 'mage249');
    }

    /**
     * @test
     */
    public function nullReturnedWhenLookingUpNonExistentFileInCache(): void
    {
        $absFilename = $this->cache->getFile('test.txt');

        self::assertNull($absFilename);
    }

    /**
     * @test
     */
    public function absoluteFilenameReturnedWhenLookingUpExistentFileInCache(): void
    {
        vfsStream::create(
            ['03' => ['ef' => ['4b6fcb2d521ef0fd442a5301e7932d16cc9f375a.php' => 'Lorem ipsum']]],
            $this->root
        );

        $absFilename = $this->cache->getFile('test.txt');

        self::assertSame($absFilename, vfsStream::url('tmp/03/ef/4b6fcb2d521ef0fd442a5301e7932d16cc9f375a.php'));
    }

    /**
     * @test
     */
    public function addingFileToCacheSucceeds(): void
    {
        $writtenFilename = $this->cache->putFile('test.txt', 'Lorem ipsum');
        $absFilename = $this->cache->getFile('test.txt');

        self::assertSame($absFilename, vfsStream::url('tmp/03/ef/4b6fcb2d521ef0fd442a5301e7932d16cc9f375a.php'));
        self::assertSame($absFilename, $writtenFilename);
    }

    /**
     * @test
     */
    public function contentsAreWrittenToTheReturnedFile(): void
    {
        $writtenFilename = $this->cache->putFile('test.txt', 'Lorem ipsum');

        self::assertSame('Lorem ipsum', file_get_contents($writtenFilename));
    }

    /**
     * @test
     */
    public function addingFileToCacheFails(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // simulate full disk
        vfsStream::setQuota(1);

        $this->cache->putFile('test.txt', 'Lorem ipsum');
    }

    /**
     * @test
     */
    public function existingCacheDirectoryIsReused(): void
    {
        $this->cache->putFile('test.txt', 'Lorem ipsum');
        $writtenFilename = $this->cache->putFile('test.txt', 'Dolor sit amet');

        self::assertSame('Dolor sit amet', file_get_contents($writtenFilename));
    }

    /**
     * @test
     */
    public function addingFileToCacheFailsWhenCacheDirectoryCannotBeCreated(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#^Failed to create directory#');

        $this->root->chmod(0000);

        $this->cache->putFile('test.txt', 'Lorem ipsum');
    }
}

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

class FileCacheStorageUnitTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;
    /**
     * @var FileCacheStorage
     */
    private $storage;

    public function setUp(): void
    {
        $this->root = vfsStream::setup('tmp');
        $this->storage = new FileCacheStorage($this->root->url());
    }

    /**
     * @test
     */
    public function nullReturnedWhenLookingUpNonExistentFileInCache(): void
    {
        $absFilename = $this->storage->load('test.txt', '');

        self::assertNull($absFilename);
    }

    /**
     * @test
     */
    public function absoluteFilenameReturnedWhenLookingUpExistentFileInCache(): void
    {
        vfsStream::create(
            ['4b' => ['6f' => ['4b6fcb2d521ef0fd442a5301e7932d16cc9f375a.php' => 'Lorem ipsum']]],
            $this->root
        );

        $absFilename = $this->storage->load('test.txt', '');

        self::assertSame($absFilename, vfsStream::url('tmp/4b/6f/4b6fcb2d521ef0fd442a5301e7932d16cc9f375a.php'));
    }

    /**
     * @test
     */
    public function addingFileToCacheSucceeds(): void
    {
        $this->storage->save('test.txt', '', 'Lorem ipsum');
        $absFilename = $this->storage->load('test.txt', '');

        self::assertSame($absFilename, vfsStream::url('tmp/4b/6f/4b6fcb2d521ef0fd442a5301e7932d16cc9f375a.php'));
    }

    /**
     * @test
     */
    public function addingFileToCacheFails(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // simulate full disk
        vfsStream::setQuota(1);

        $this->storage->save('test.txt', '', 'Lorem ipsum');
        $this->storage->load('test.txt', '');
    }
}

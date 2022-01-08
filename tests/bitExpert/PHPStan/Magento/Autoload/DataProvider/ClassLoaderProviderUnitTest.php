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

namespace bitExpert\PHPStan\Magento\Autoload\DataProvider;

use PHPUnit\Framework\TestCase;

class ClassLoaderProviderUnitTest extends TestCase
{
    /**
     * @var ClassLoaderProvider
     */
    private $dataprovider;

    protected function setUp(): void
    {
        $this->dataprovider = new ClassLoaderProvider(__DIR__ . '/../../../../../../');
    }

    /**
     * @test
     */
    public function returnsTrueForClassesFound(): void
    {
        static::assertTrue($this->dataprovider->exists(ClassLoaderProviderUnitTest::class));
    }

    /**
     * @test
     */
    public function returnsFalseWhenClassNotFound(): void
    {
        static::assertFalse($this->dataprovider->exists('SomeOtherClass'));
    }
}

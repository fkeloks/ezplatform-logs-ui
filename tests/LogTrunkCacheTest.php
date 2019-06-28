<?php

namespace EzPlatformLogsUi\Tests;

use EzPlatformLogsUi\Bundle\LogManager\LogTrunkCache;
use PHPUnit\Framework\TestCase;

/**
 * Class LogTrunkCacheTest
 *
 * @author Florian Bouché <contact@florian-bouche.fr>
 *
 * @package EzPlatformLogsUi\Tests
 */
class LogTrunkCacheTest extends TestCase {

    /** @var string */
    private const LOG_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'valid.log';

    /** @var LogTrunkCache */
    private $logTrunkCache;

    public function setUp(): void {
        $logsPath = self::LOG_FILE_PATH;
        $cacheDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'symfony-cache';

        $this->logTrunkCache = new LogTrunkCache($logsPath, $cacheDirectory, 'ezplatform_logs_ui_tests');
    }

    public function testGetCacheKeyMethod(): void {
        $encodedLogFilPath = md5(self::LOG_FILE_PATH);

        $this->assertSame('ezplatform_logs_ui_tests.logs.' . $encodedLogFilPath, $this->logTrunkCache->getCacheKey());
        $this->assertSame('ezplatform_logs_ui_tests.tests.' . $encodedLogFilPath, $this->logTrunkCache->getCacheKey('tests'));
    }

    public function testGetChunkIdentifierMethod(): void {
        $encodedLogFilPath = md5(self::LOG_FILE_PATH);

        $this->assertSame(
            'ezplatform_logs_ui_tests.logs.' . $encodedLogFilPath . '.chunk.1',
            $this->logTrunkCache->getChunkIdentifier(1)
        );

        $this->assertSame(
            'ezplatform_logs_ui_tests.logs.' . $encodedLogFilPath . '.chunk.123',
            $this->logTrunkCache->getChunkIdentifier(123)
        );
    }

    public function testSetChunkMethod(): void {
        // With default TTL
        $this->assertTrue($this->logTrunkCache->setChunk(1, 123));

        // With TTL
        $this->assertTrue($this->logTrunkCache->setChunk(2, 456, 1000));
    }

    public function testHasChunkMethod(): void {
        $this->assertTrue($this->logTrunkCache->hasChunk(1));
        $this->assertTrue($this->logTrunkCache->hasChunk(2));

        $this->assertFalse($this->logTrunkCache->hasChunk(3));
    }

    public function testGetChunkMethod(): void {
        $this->assertSame($this->logTrunkCache->getChunk(1), 123);
        $this->assertSame($this->logTrunkCache->getChunk(2), 456);

        $this->assertNull($this->logTrunkCache->getChunk(3));
        $this->assertSame($this->logTrunkCache->getChunk(3, 789), 789);
    }

}

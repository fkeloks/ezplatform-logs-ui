<?php

namespace EzPlatformLogsUi\Tests;

use EzPlatformLogsUi\Bundle\Filesystem\LogFile;
use PHPUnit\Framework\TestCase;

/**
 * Class LogFileTest
 *
 * @author Florian Bouché <contact@florian-bouche.fr>
 *
 * @package EzPlatformLogsUi\Tests
 */
class LogFileTest extends TestCase {

    /** @var LogFile */
    private $validLogFile;

    /** @var LogFile */
    private $emptyLogFile;

    /** @var LogFile[] */
    private $invalidLogFiles = [];

    public function setUp(): void {
        $logsPath = __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;

        $this->validLogFile = new LogFile($logsPath . 'valid.log');
        $this->emptyLogFile = new LogFile($logsPath . 'empty.log');

        $this->invalidLogFiles = [
            'partially' => new LogFile($logsPath . 'partially-invalid.log'),
            'full'      => new LogFile($logsPath . 'full-invalid.log'),
        ];
    }

    public function testValidLogFileReadingAndParsing(): void {
        $lines = $this->validLogFile->read();
        $this->assertIsArray($lines);
        $this->assertCount(32, $lines);

        $lines = $this->validLogFile->parse($lines);
        $this->assertIsArray($lines);
        $this->assertCount(32, $lines);
        $this->assertSame([
            'date'    => '2019-06-23 16:20:06',
            'logger'  => 'app',
            'level'   => 'WARNING',
            'message' => 'ConfigResolver was used by "ezplatform\var\cache\dev\ContainerAxajx6t\getConsole_Command_EzsystemsEzplatformgraphqlCommandGenerateplatformschemacommand(@ezpublish.siteaccessaware.service.content_type)" before SiteAccess was initialized, loading parameter(s) "$languages$". As this can cause very hard to debug issues, try to use ConfigResolver lazily, make the affected commands lazy, make the service lazy or see if you can inject another lazy service.',
            'context' => '[]',
            'extra'   => '[]',
            'class'   => 'warning'
        ], $lines[0]);
    }

    public function testEmptyLogFileReadingAndParsing(): void {
        $lines = $this->emptyLogFile->read();
        $this->assertIsArray($lines);
        $this->assertEmpty($lines);

        $lines = $this->emptyLogFile->parse($lines);
        $this->assertIsArray($lines);
        $this->assertEmpty($lines);
    }

    public function testInvalidLogFileReadingAndParsing(): void {
        // Partially invalid
        $lines = $this->invalidLogFiles['partially']->read();
        $this->assertIsArray($lines);
        $this->assertCount(17, $lines);

        $lines = $this->invalidLogFiles['partially']->parse($lines);
        $this->assertIsArray($lines);
        $this->assertCount(10, $lines);

        // Full invalid
        $lines = $this->invalidLogFiles['full']->read();
        $this->assertIsArray($lines);
        $this->assertCount(7, $lines);

        $lines = $this->invalidLogFiles['full']->parse($lines);
        $this->assertIsArray($lines);
        $this->assertEmpty($lines);
    }

}

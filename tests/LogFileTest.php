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
        $lastLines = [
            '[2019-06-23 16:20:29] php.INFO: User Deprecated: Checking for the initialization of the "ezpublish.siteaccessaware.service.object_state" private service is deprecated since Symfony 3.4 and won\'t be supported anymore in Symfony 4.0. {"exception":"[object] (ErrorException(code: 0): User Deprecated: Checking for the initialization of the \"ezpublish.siteaccessaware.service.object_state\" private service is deprecated since Symfony 3.4 and won\'t be supported anymore in Symfony 4.0. at ezplatform\\\\vendor\\\\symfony\\\\symfony\\\\src\\\\Symfony\\\\Component\\\\DependencyInjection\\\\Container.php:364)"} []',
            '[2019-06-23 16:20:29] php.INFO: User Deprecated: Checking for the initialization of the "ezpublish.siteaccessaware.service.content_type" private service is deprecated since Symfony 3.4 and won\'t be supported anymore in Symfony 4.0. {"exception":"[object] (ErrorException(code: 0): User Deprecated: Checking for the initialization of the \"ezpublish.siteaccessaware.service.content_type\" private service is deprecated since Symfony 3.4 and won\'t be supported anymore in Symfony 4.0. at ezplatform\\\\vendor\\\\symfony\\\\symfony\\\\src\\\\Symfony\\\\Component\\\\DependencyInjection\\\\Container.php:364)"} []',
            '[2019-06-23 16:20:29] php.INFO: User Deprecated: Checking for the initialization of the "ezpublish.siteaccessaware.service.content" private service is deprecated since Symfony 3.4 and won\'t be supported anymore in Symfony 4.0. {"exception":"[object] (ErrorException(code: 0): User Deprecated: Checking for the initialization of the \"ezpublish.siteaccessaware.service.content\" private service is deprecated since Symfony 3.4 and won\'t be supported anymore in Symfony 4.0. at ezplatform\\\\vendor\\\\symfony\\\\symfony\\\\src\\\\Symfony\\\\Component\\\\DependencyInjection\\\\Container.php:364)"} []'
        ];

        $lines = $this->validLogFile->tail();
        $this->assertIsArray($lines);
        $this->assertCount(32, $lines);
        $this->assertSame($lastLines, array_slice($lines, 0, 3));

        $lines = $this->validLogFile->parse($lines);
        $this->assertIsArray($lines);
        $this->assertCount(32, $lines);
        $this->assertSame([
            'date'    => '2019-06-23 16:20:29',
            'logger'  => 'php',
            'level'   => 'INFO',
            'message' => 'User Deprecated: Checking for the initialization of the "ezpublish.siteaccessaware.service.object_state" private service is deprecated since Symfony 3.4 and won\'t be supported anymore in Symfony 4.0. {"exception":"[object] (ErrorException(code: 0): User Deprecated: Checking for the initialization of the \"ezpublish.siteaccessaware.service.object_state\" private service is deprecated since Symfony 3.4 and won\'t be supported anymore in Symfony 4.0. at',
            'context' => 'ezplatform\\\\vendor\\\\symfony\\\\symfony\\\\src\\\\Symfony\\\\Component\\\\DependencyInjection\\\\Container.php:364)"}',
            'extra'   => '[]',
            'class'   => 'info'
        ], $lines[0]);
    }

    public function testEmptyLogFileReadingAndParsing(): void {
        $lines = $this->emptyLogFile->tail();
        $this->assertIsArray($lines);
        $this->assertEmpty($lines);

        $lines = $this->emptyLogFile->parse($lines);
        $this->assertIsArray($lines);
        $this->assertEmpty($lines);
    }

    public function testInvalidLogFileReadingAndParsing(): void {
        // Partially invalid
        $lines = $this->invalidLogFiles['partially']->tail();
        $this->assertIsArray($lines);
        $this->assertCount(17, $lines);

        $lines = $this->invalidLogFiles['partially']->parse($lines);
        $this->assertIsArray($lines);
        $this->assertCount(10, $lines);

        // Full invalid
        $lines = $this->invalidLogFiles['full']->tail();
        $this->assertIsArray($lines);
        $this->assertCount(7, $lines);

        $lines = $this->invalidLogFiles['full']->parse($lines);
        $this->assertIsArray($lines);
        $this->assertEmpty($lines);
    }

}

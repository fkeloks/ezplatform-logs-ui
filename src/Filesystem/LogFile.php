<?php

namespace EzPlatformLogsUi\Bundle\Filesystem;

use EzPlatformLogsUi\Bundle\Parser\LineLogParser;

/**
 * Class LogFile
 *
 * @author Florian Bouché <contact@florian-bouche.fr>
 *
 * @package EzPlatformLogsUi\Bundle\Filesystem
 */
class LogFile {

    /** @var array Log levels for Bootstrap classes */
    private const LOG_LEVELS = [
        'DEBUG'     => 'secondary',
        'INFO'      => 'info',
        'NOTICE'    => 'info',
        'WARNING'   => 'warning',
        'ERROR'     => 'danger',
        'CRITICAL'  => 'danger',
        'ALERT'     => 'danger',
        'EMERGENCY' => 'danger'
    ];

    /** @var string Path of current log file */
    private $filePath;

    /**
     * LogFile constructor.
     *
     * @param string $filePath Path of current log file
     */
    public function __construct(string $filePath) {
        $this->filePath = $filePath;
    }

    /**
     * Read lines from current log file
     *
     * @param int|bool $limit
     *
     * @return array
     */
    public function read($limit = 1000): array {
        $lines = [];
        $handle = fopen($this->filePath, 'rb');

        while (!feof($handle)) {
            $lines[] = trim(fgets($handle));

            if ($limit && count($lines) === $limit) {
                break;
            }
        }

        fclose($handle);

        return array_filter($lines, 'mb_strlen');
    }

    /**
     * Parse log line
     *
     * @param array $lines
     *
     * @return array
     */
    public function parse(array $lines): array {
        $lines = array_map(static function ($log) {
            $log = (new LineLogParser)->parse($log);

            if (array_key_exists('level', $log) && array_key_exists($log['level'], self::LOG_LEVELS)) {
                $log['class'] = self::LOG_LEVELS[$log['level']];
            }

            return $log;
        }, $lines);

        return array_filter($lines, 'count');
    }

}

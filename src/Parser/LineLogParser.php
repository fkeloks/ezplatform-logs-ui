<?php

namespace EzPlatformLogsUi\Bundle\Parser;

use Exception;

/**
 * Class LineLogParser
 *
 * @author Florian Bouché <contact@florian-bouche.fr>
 *
 * @package EzPlatformLogsUi\Bundle\Parser
 */
class LineLogParser {

    /** @var string */
    private const PARSER_PATTERN = '/\[(?P<date>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<message>.*[^ ]+) (?P<context>[^ ]+) (?P<extra>[^ ]+)/';

    /** @var array */
    private const PARSER_GROUPS = ['date', 'logger', 'level', 'message', 'context', 'extra'];

    /**
     * @param string $log
     *
     * @return array
     */
    public function parse(string $log): array {
        try {
            if (!is_string($log) || $log === '') {
                return [];
            }

            $match = preg_match(self::PARSER_PATTERN, $log, $matches);
            if (!$match) {
                return [];
            }

            foreach (self::PARSER_GROUPS as $group) {
                if (!array_key_exists($group, $matches)) {
                    return [];
                }
            }

            return [
                'date'    => $matches['date'],
                'logger'  => $matches['logger'],
                'level'   => $matches['level'],
                'message' => $matches['message'],
                'context' => $matches['context'],
                'extra'   => $matches['extra']
            ];
        } catch (Exception $exception) {
            return [];
        }
    }

}

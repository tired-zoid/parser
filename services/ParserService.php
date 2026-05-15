<?php

namespace app\services;

use UAParser\Parser;

class ParserService
{
    private Parser $uaParser;

    public function __construct()
    {
        $this->uaParser = Parser::create();
    }
    public function parseLine(string $line): ?array
    {
        $pattern = '/^(?<ip>\S+) .* \[(?<date>[^\]]+)\] "\w+ (?<url>\S+) HTTP\/[^"]+" \d+ \d+ "[^"]*" "(?<agent>.+)"$/';

        if (!preg_match($pattern, $line, $matches)) {
            return null;
        }

        $userAgent = $this->uaParser->parse($matches['agent']);
        $browser = $userAgent->ua->family ?? 'Unknown';
        $os = $userAgent->os->family ?? 'Unknown';

        $arch = str_contains($matches['agent'], 'x86_64') ? 'x64' : 'x86';

        return [
            'ip' => $matches['ip'],
            'requested_at' => $this->parseDate($matches['date']),
            'url' => $matches['url'],
            'user_agent' => $matches['agent'],
            'browser' => $browser,
            'os' => $os,
            'architecture' => $arch,
        ];
    }

    private function parseDate(string $date): string
    {
        return date('Y-m-d H:i:s', strtotime($date));
    }
}

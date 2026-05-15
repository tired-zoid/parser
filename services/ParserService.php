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

        $ua = strtolower($matches['agent']);

        $arch = match (true) {
            str_contains($ua, 'win64'),
            str_contains($ua, 'x64'),
            str_contains($ua, 'amd64'),
            str_contains($ua, 'wow64') => 'x64',

            str_contains($ua, 'i386'),
            str_contains($ua, 'i686'),
            str_contains($ua, 'x86') => 'x86',

            default => 'unknown',
        };

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

<?php

namespace app\commands;

use yii\console\Controller;
use UAParser\Parser;

class LogController extends Controller
{
    public function actionImport($path)
    {
        $parser = Parser::create();
        $result = $parser->parse($userAgent);
        $browser = $result->ua->family;
        $os = $result->os->family;
        $architecture = str_contains($userAgent, 'x86_64') ? 'x64' : 'x86';
        $handle = fopen($path, 'r');

        while (($line = fgets($handle)) !== false) {

            var_dump($line);

        }

        fclose($handle);
    }
}

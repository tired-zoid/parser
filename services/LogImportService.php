<?php

namespace app\services;

use Yii;

class LogImportService
{
    private ParserService $parser;

    public function __construct()
    {
        $this->parser = new ParserService();
    }
    public function import(string $path): void
    {
        $handle = fopen($path, 'r');
        $batch = [];

        while (($line = fgets($handle)) !== false) {
            $data = $this->parser->parseLine($line);

            if (!$data) continue;

            $batch[] = $data;

            if (count($batch) >= 1000) {
                $this->flush($batch);
                $batch = [];
            }
        }

        if ($batch) {
            $this->flush($batch);
        }

        fclose($handle);
    }

    private function flush(array $batch)
    {
        Yii::$app->db->createCommand()
            ->batchInsert('logs',
                ['ip','requested_at','url','user_agent','browser','os','architecture'],
                $batch)
            ->execute();
    }

}

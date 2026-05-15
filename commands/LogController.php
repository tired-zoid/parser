<?php

namespace app\commands;

use yii\console\Controller;

class LogController extends Controller
{
    public function actionImport($path)
    {
        $handle = fopen($path, 'r');

        while (($line = fgets($handle)) !== false) {

            var_dump($line);

        }

        fclose($handle);
    }
}

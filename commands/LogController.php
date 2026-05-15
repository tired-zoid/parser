<?php

namespace app\commands;

use app\services\LogImportService;
use yii\console\Controller;

class LogController extends Controller
{
    public function actionImport($path)
    {
        $service = new LogImportService();
        $service->import($path);
    }
}

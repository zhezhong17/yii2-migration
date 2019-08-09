<?php

namespace zhezhong17\migration;

use Yii;
use yii\console\Exception;
use yii\helpers\Console;
use yii\helpers\ArrayHelper;
use yii\console\controllers\MigrateController;

/**
 * 解析命令
 */
class ConsoleController extends MigrateController
{
    public function actionBackup ($name)
    {
        $allTables = Yii::$app->db->createCommand('SHOW TABLE STATUS')->queryAll();

        $name = trim($name, ',');

        // echo $name;

        // sleep(10);

        return true;
    }
}


<?php

namespace zhezhong17\migration;

use Yii;
use yii\console\Exception;
use yii\helpers\Console;
use yii\helpers\ArrayHelper;
use yii\console\controllers\MigrateController;

/**
 * 参考了e282486518/yii2-console-migration包   根据自己的想法开发了这个包
 * 解析命令
 */
class ConsoleController extends MigrateController
{
    public function actionReverse($name)
    {
        // 获取全部表名
        $talbeData = Yii::$app->db->createCommand('SHOW TABLE STATUS')->queryAll();
        $talbeData = array_map('array_change_key_case', $talbeData);

        // 视图只能最后生成
        $allTables = [];
        $viewArr = [];
        foreach ($talbeData as $item) {
            if ($item['comment'] == 'VIEW') {
                array_push($allTables, $item['name']);
                array_push($viewArr, $item['name']);
            } else {
                array_unshift($allTables, $item['name']);
            }
        }

        $name = trim($name, ',');

        if ($name == 'all') {
            $userTables = $allTables;
        } elseif (strpos($name, ',')) {
            $userTables = explode(',', $name);
        } else {
            /* 备份一个数据表 */
            $userTables = [$name];
        }

        // 用户输入的表名也要保持视图最后生成
        $tables = [];
        foreach ($allTables as $allTable) {
            if (in_array($allTable, $userTables)) {
                array_push($tables, $allTable);
            }
        }

        // 创建migration
        foreach ($userTables as $table) {
            $this->stdout("正在为{$table}表制作" . "\n");

            $migrate = Yii::createObject([
                'class' => 'zhezhong17\migration\components\MigrateCreate',
                'migrationPath' => '@app/migrations'
            ]);
            $migrate->create($table, $viewArr);
            unset($migrate);
        }

        $this->stdout("All success. \n", Console::FG_GREEN);
    }
}

<?php

namespace zhezhong17\migration\components;

use Yii;
use yii\base\BaseObject;
use yii\helpers\FileHelper;
use yii\base\View;

class MigrateCreate extends BaseObject
{
    protected $upStr;

    protected $downStr;

    public $migrationPath = '@app/migration';


    /**
     * 创建migratge
     */
    public function create($table, $viewArr = [])
    {
        $this->generateUp($table);

        $this->generateDown($table);

        $this->createMigrateFile($table, $viewArr);

        return true;
    }


    /**
     * 组装 migrate的 UP方法
     */
    public function generateUp($table)
    {
        $this->upStr = new QutputString(['tabLevel' => 2]);

        // 取消外键约束
        $this->upStr->addStr('/* 取消外键约束 */');
        $this->upStr->addStr('$this->execute(\'SET foreign_key_checks = 0\');');
        $this->upStr->addStr('');

        // 获取表结构
        $table_tructure = Yii::$app->db->createCommand('show create table `' . $table . '`')->queryOne();
        $count = count($table_tructure);

        // 表 && 视图处理
        $this->upStr->addStr('/* 创建表SQL语句 */');

        if ($count == 2) {
            $sql = end($table_tructure);
        } else {
            $create_view = "DROP TABLE IF EXISTS `{$table_tructure['View']}`";
            $sql = $create_view . '; ' . $table_tructure['Create View'];
        }

        $this->upStr->addStr('$sql = ' . '"' . $sql . '";');
        $this->upStr->addStr('');
        $this->upStr->addStr('$this->execute($sql);');

        $this->upStr->addStr('');

        // 设置外键约束
        $this->upStr->addStr('/* 设置外键约束 */');
        $this->upStr->addStr('$this->execute(\'SET foreign_key_checks = 1\');');

        return true;
    }
 

    /**
     * 组装 migrate的 safeDown方法
     */
    public function generateDown($table)
    {
        $this->downStr = new QutputString(['tabLevel' => 2]);

        // 取消外键约束
        $this->downStr->addStr('/* 取消外键约束 */');
        $this->downStr->addStr('$this->execute(\'SET foreign_key_checks = 0\');');
        $this->downStr->addStr('');

        // 删除表
        $this->downStr->addStr('/* 删除表 */');
        $this->downStr->addStr('$this->dropTable(\'{{%' . $this->getTableName($table) . '}}\');');
        $this->downStr->addStr('');

        // 设置外键约束
        $this->downStr->addStr('/* 设置外键约束 */');
        $this->downStr->addStr('$this->execute(\'SET foreign_key_checks = 1;\');');

        return true;
    }

    /**
     * 创建migrate模板
     */
    public function createMigrateFile($table, $viewArr)
    {
        // 生成模板
        $path = Yii::getAlias($this->migrationPath);
        if (! is_dir($path)) {
            FileHelper::createDirectory($path);
        }

        // 确保视图被最后执行
        $name = 'm' . gmdate('ymd_His') . '_' . $this->getTableName($table);
        if (in_array($table, $viewArr)) {
            $name = 'm' . gmdate('ymd_His', time() + 1) . '_' . $this->getTableName($table);
        }
        
        $file = $path . DIRECTORY_SEPARATOR . $name . '.php';

        $view = new View();
        $content = $view->renderFile(dirname(__DIR__) . '/views/migration.php', [
            'className' => $name,
            'up' => $this->upStr->output() . "\n",
            'down' => $this->downStr->output() . "\n",
        ]);

        file_put_contents($file, $content);

        return true;
    }

    /**
     * 获取表名
     */
    public function getTableName($table)
    {
        $prefix = \Yii::$app->db->tablePrefix;

        return str_replace($prefix, '', $table);
    }
}

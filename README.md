
参考了e282486518/yii2-console-migration等其他很多包, 但它们都没有达到我的需求

=============================

我们的目标是百分百还原


包括但不限于(表结构 索引 字段长度 字段排序规则 数值精度 视图等)

所以诞生了它

如果你发现生成的结果不能百分百还原，请邮件提醒我。会继续改进的。


=============================

安装 Installation
--------------

```
    composer require zhezhong17/yii2-migration "@dev"
```

OR

```
    "zhezhong17/yii2-migration": "*"
```


还需要的工作
----

在```console\config\main.php```
OR ```config\console.php``` 中添加 :

```php
'controllerMap' => [
    'migrate' => [
        'class' => 'zhezhong17\migration\ConsoleController',
    ]
],
```

在命令行中使用方式：
```
php ./yii migrate/reversen all # 逆向生成全部表
php ./yii migrate/reversen table1 #备份一张表
php ./yii migrate/reversen table1,table2,table3... #备份多张表 

php ./yii migrate/up #恢复全部表
```


其他问题
````
    1. php ./yii migrate/reversen table1, table2, table3... 这种写法是错误的，逗号后面有空格。参数识别不出来，请注意啊

    2. 为了保持百分百一致，所以生成的是SQL语句, migrations只是执行SQL语句。 不是我们平时写的那种migrations格式。 (不喜欢这个格式请换其他包， 当然后续也可能会改进生成的格式)  

    3. 我们的目标是百分百一致

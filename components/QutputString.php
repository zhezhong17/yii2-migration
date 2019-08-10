<?php

namespace zhezhong17\migration\components;

use Yii;
use yii\base\BaseObject;

class QutputString extends BaseObject
{
    public $nw = "\n";

    public $tab = '    ';

    public $outputStringArray = [];

    /**
     * @var int
     */
    public $tabLevel = 0;

    public function addStr($str)
    {
        $str = str_replace($this->tab, '', $str);
        $str = str_replace(PHP_EOL, PHP_EOL . '          ', $str);
        $this->outputStringArray[] = str_repeat($this->tab, $this->tabLevel) . $str;
    }

    public function output()
    {
        return implode($this->nw, $this->outputStringArray);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2018/1/9
 * Time: 11:11
 */

namespace common\service;

use common\thrift\ThriftService;
use Yii;
use yii\base\Exception;
use common\util\Code;


class AdditionService extends ThriftService
{
    //子类必须声明该属性,以指向当前类的单实例
    protected static $instance = null;

    //对应后端相应的service名称
    public $service = 'AdditionService';

    //是否使用了多路协议，是则为true，否则为false
    public $multiplexed = true;

}
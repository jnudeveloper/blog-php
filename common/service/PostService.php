<?php
/**
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2018/1/11
 * Time: 19:51
 */

namespace common\service;
use common\thrift\ThriftService;


class PostService extends ThriftService
{
    //子类必须声明该属性,以指向当前类的单实例
    protected static $instance = null;

    //对应后端相应的service名称
    public $service = 'PostService';

    //是否使用了多路协议，是则为true，否则为false
    public $multiplexed = true;

    /**
     * 根据id获取相应的post
     * @param int $id post id
     * @return false|array
     */
    public static function findById($id){
        $request = self::getRequest();
        $request->version = "0.0.1";
        $request->data = json_encode([
            'id' => $id
        ]);

        return self::call('findById', $request, __METHOD__);
    }

    /**
     * 获取所有的post
     * @return false|array
     */
    public static function findAll(){
        $request = self::getRequest();
        $request->version = "0.0.1";

        return self::call('findAll', $request, __METHOD__);
    }

}
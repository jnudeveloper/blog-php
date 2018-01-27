<?php
/**
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2018/1/27
 * Time: 11:13
 */

namespace api\models\thrift;


use yii\base\Model;
use common\service\PostService;

class Post extends Model
{
    public static function getAllPosts(){
        $result = PostService::findAll();
        $posts = !is_array($result) ? [] : $result;

        return $posts;
    }


    public static function getPost($id){
        $result = PostService::findById(intval($id));
        $post = !is_array($result) ? [] : $result;

        return $post;
    }

    public static function getPostsWithPagination(){
        $result = PostService::getPostsWithPagination();
        $post = !is_array($result) ? [] : $result;

        return $post;
    }
}
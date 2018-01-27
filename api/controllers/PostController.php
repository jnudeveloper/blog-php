<?php
/**
 * Created by PhpStorm.
 * User: Darkgel
 * Date: 2017/12/20
 * Time: 22:32
 */

namespace api\controllers;

use src\data\ArrayDataProvider;
use yii\rest\Controller;//如果resource不是ActiveRecord的话就使用这个
use api\models\thrift\Post;

class PostController extends Controller
{
    public $serializer = [
        'class' => 'src\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function actionIndex(){
        $posts = Post::getAllPosts();

        return new ArrayDataProvider([
            'allModels' => $posts,
        ]);
    }

    public function actionView($id){
        $post = Post::getPost($id);

        return $post;
    }
}
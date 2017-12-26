<?php
/**
 * Created by PhpStorm.
 * User: Darkgel
 * Date: 2017/12/20
 * Time: 22:32
 */

namespace api\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use common\models\Post;
use yii\rest\Controller;//如果resource不是ActiveRecord的话就使用这个

class PostController extends ActiveController
{
    public $modelClass = 'common\models\Post';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => Post::find(),
        ]);
    }
}
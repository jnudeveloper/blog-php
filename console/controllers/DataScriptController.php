<?php
/**
 * 该controller用于往数据库中添加测试数据,伪造数据
 *
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2017/12/26
 * Time: 14:28
 */

namespace console\controllers;

use yii\console\Controller;
use yii\db\Migration;

class DataScriptController extends Controller
{

    public function actionAddPost(){
        $title = "这里是文章标题";
        $abstract = "这里是摘要";
        $content = "这里是文章内容";

        $migration = new Migration();

        for($i = 0; $i < 8; $i++){
            $migration->insert('{{%post}}', [
                'title' => $title.$i,
                'abstract' => $abstract.$i,
                'content' => $content.$i,
                'status' => $i % 2,
                'create_time' => time(),
                'update_time' => time(),
                'author_id' => $i % 3,
                'approve_num' => $i % 4,
                'collect_num' => $i % 3,
                'comment_num' => $i % 2,
            ]);
        }

    }
}
<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post`.
 */
class m171226_033901_create_post_table extends Migration
{
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        $this->createTable('{{%post}}', [
            'id' => 'INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'title' => 'VARCHAR(256) DEFAULT \'\' COMMENT "文章标题"',
            'abstract' => 'VARCHAR(512) DEFAULT \'\' COMMENT "摘要"',
            'content' => 'TEXT COMMENT "文章内容"',
            'status' => 'TINYINT DEFAULT 1 COMMENT "状态，0已删除1草稿2已发布"',
            'create_time' => 'INT(10) DEFAULT 0 COMMENT "创建时间"',
            'update_time' => 'INT(10) DEFAULT 0 COMMENT "更新时间"',
            'author_id' => 'INT(10) DEFAULT 0 COMMENT "作者"',
            'approve_num' => 'INT(10) DEFAULT 0 COMMENT "点赞数"',
            'collect_num' => 'INT(10) DEFAULT 0 COMMENT "收藏数"',
            'comment_num' => 'INT(10) DEFAULT 0 COMMENT "评论数"',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%post}}');
    }
}

<?php

namespace common\models;

use Yii;
use yii\web\Link; // represents a link object as defined in JSON Hypermedia API Language.
use yii\web\Linkable;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%post}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $abstract
 * @property string $content
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $author_id
 * @property integer $approve_num
 * @property integer $collect_num
 * @property integer $comment_num
 */
class Post extends \yii\db\ActiveRecord implements Linkable
{
    //文章状态，对应字段status
    const STATUS_DELETED = 0;//已删除
    const STATUS_DRAFT = 1;//草稿，status字段的默认值
    const STATUS_PUBLISHED = 2;//已发布

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%post}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['status', 'create_time', 'update_time', 'author_id', 'approve_num', 'collect_num', 'comment_num'], 'integer'],
            [['title'], 'string', 'max' => 256],
            [['abstract'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '文章标题',
            'abstract' => '摘要',
            'content' => '文章内容',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'author_id' => '作者',
            'approve_num' => '点赞数',
            'collect_num' => '收藏数',
            'comment_num' => '评论数',
        ];
    }

    /**
     * @inheritdoc
     * @return PostQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PostQuery(get_called_class());
    }

    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['post/view', 'id' => $this->id], true),
            'edit' => Url::to(['post/view', 'id' => $this->id], true),
            'index' => Url::to(['posts'], true),
        ];
    }
}

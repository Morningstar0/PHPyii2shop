<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property integer $article_category_id
 * @property integer $sort
 * @property integer $status
 * @property integer $create_time
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','intro','sort','status'],'required'],
            [['intro'], 'string'],
            [['article_category_id', 'sort', 'status', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            'article_category_id' => '文章分类id',
            'sort' => '排序',
            'status' => '状态(-1删除 0隐藏 1正常)',
            'create_time' => '创建时间',
        ];
    }
    //关联表
    public function getArticleCategory(){
        //hasOne() 代表对应一个  参数1 class 关联对象的类名
        //参数2 表示对应的键 [k=>v]  k表示关联对象的主键  v表示当前对象的关联主键
        return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
    }
}

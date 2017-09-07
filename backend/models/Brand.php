<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property string $logo
 * @property integer $sort
 * @property integer $status
 */
class Brand extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brand';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','intro','sort','status'],'required'],
            [['intro'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 255],
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
            'logo' => 'LOGO图片',
            'sort' => '排序',
            'status' => '状态(-1删除 0隐藏 1正常)',
        ];
    }
    //保存图片.
    public function saveImg(){
        if($this->file){
            $file = '/upload/brand/' . uniqid() . '.' . $this->file->getExtension();
            $this->logo=$file;
            return $this->file->saveAs(\Yii::getAlias('@webroot') . $file, false);
        }
        return true;
    }
    //删除图片.
    public function delImg(){
        unlink(\Yii::getAlias('@webroot').$this->logo);
    }
}

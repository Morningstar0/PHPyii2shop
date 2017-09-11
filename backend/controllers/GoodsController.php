<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;
use yii\data\Sort;

class GoodsController extends \yii\web\Controller
{
    //商品数据列表显示
    public function actionIndex()
    {
        //排序规则.
        $sort = new Sort([
            'attributes' => ['id', 'name', 'sn','stock'],
        ]);
        //查处所有的商品数据.
        $Goods = Goods::find();
        $pager = new Pagination([
            'totalCount' => $Goods->where(['>','status',0])->count(),//总数据条数.
            'defaultPageSize' => 5//每页多少条.
        ]);
        //条件查询.offset为偏移量.limit为取多少条.
        $Goods = $Goods->limit($pager->limit)->offset($pager->offset)->orderBy($sort->orders)->where(['>','status',0])->all();
        return $this->render('index', ['goods' => $Goods,'sort' => $sort, 'pager' => $pager]);
    }
    //添加商品.
    public function actionAdd()
    {
        $goods = new Goods();
        $goods_intro = new GoodsIntro();
        $brand = Brand::find()->all();
        $request = \Yii::$app->request;
        if ($request->isPost){
            $goods->load($request->post());
            $goods_intro->load($request->post());
            if ($goods->validate() && $goods_intro->validate()){
                $goods_category = GoodsCategory::findOne(['id'=>$goods->goods_category_id]);
                if ($goods_category->isLeaf()){//是否是叶子节点.(是否有子节点.)
                    //查出当天是否有商品上架.
                    $goods_day_count = GoodsDayCount::findOne(['day'=>date("Ymd",time())]);
                    if ($goods_day_count){
                        $goods_day_count->count += 1;//给数量加一.
                        $goods->sn = date("Ymd",time()).sprintf('%05d',$goods_day_count->count);
                    }else{
                        $goods_day_count = new GoodsDayCount();
                        $goods_day_count->day = date("Ymd",time());
                        $goods_day_count->count = 1;
                        $goods->sn = date("Ymd",time()).'00001';
                    }
                    $goods_day_count->save();
                    $goods->create_time = time();
                    $goods->view_times = 0;
                    $goods->save();
                    //var_dump($goods->getErrors());exit;
                    $goods_intro->goods_id = $goods->id;
                    $goods_intro->save();
                    \Yii::$app->session->setFlash('success', '添加成功');
                    return $this->redirect(['goods/index']);
                }else{
                    \Yii::$app->session->setFlash('warning','请选择正确的分类');
                }
            } else {
                //验证失败,获取错误信息.
                var_dump($goods->getErrors(),$goods_intro->getErrors());
                exit;
            }
        }
        $goods->status = 1;
        $goods->is_on_sale = 0;
        return $this->render('add', ['goods' => $goods,'goods_intro' => $goods_intro,'brand'=>$brand]);
    }
    //修改商品数据.
    public function actionEdit($id)
    {
        $goods = Goods::findOne(['id'=>$id]);
        $goods_intro = GoodsIntro::findOne(['goods_id'=>$id]);
        $brand = Brand::find()->all();
        $request = \Yii::$app->request;
        if ($request->isPost){
            $goods->load($request->post());
            $goods_intro->load($request->post());
            if ($goods->validate() && $goods_intro->validate()){
                $goods_category = GoodsCategory::findOne(['id'=>$goods->goods_category_id]);
                if ($goods_category->isLeaf()){//是否是叶子节点.(是否有子节点.)
                    $goods->save();
                    //var_dump($goods->getErrors());exit;
                    $goods_intro->save();
                    \Yii::$app->session->setFlash('success', '添加成功');
                    return $this->redirect(['goods/index']);
                }else{
                    \Yii::$app->session->setFlash('warning','请选择正确的分类');
                }
            } else {
                //验证失败,获取错误信息.
                var_dump($goods->getErrors(),$goods_intro->getErrors());
                exit;
            }
        }
        $goods->status = 1;
        $goods->is_on_sale = 0;
        return $this->render('add', ['goods' => $goods,'goods_intro' => $goods_intro,'brand'=>$brand]);
    }
    //删除数据
    public function actionDelete()
    {
        $id = \Yii::$app->request->post('id');
        $model = Goods::findOne($id);
        if($model){
            $model->status = 0;
            $model->save();
            return 'success';
        }
        return 'fail';
    }
    //显示商品详情.
    public function actionShow($id){
        $goods = Goods::findOne(['id'=>$id]);
        $goods_intro = GoodsIntro::findOne(['goods_id'=>$id]);
        return $this->render('show', ['goods' => $goods,'goods_intro' => $goods_intro]);
    }
    //添加相册
    public function actionAlbum($id){
        $goods_gallery = new GoodsGallery();
        $request = \Yii::$app->request;
        if ($request->isPost){
            $goods_gallery->load($request->post());
            if ($goods_gallery->validate()){
                $goods_gallery->goods_id = $id;
                $goods_gallery->save();
            } else {
                //验证失败,获取错误信息.
                var_dump($goods_gallery->getErrors());
                exit;
            }
        }
        return $this->render('album', ['goods_gallery' => $goods_gallery]);
    }

    //文件上传.
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload/goods',
                'baseUrl' => '@web/upload/goods',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                // 'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /*  'format' => function (UploadAction $action) {
                      $fileext = $action->uploadfile->getExtension();
                      $filename = sha1_file($action->uploadfile->tempName);
                      return "{$filename}.{$fileext}";
                  },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //$action->output['fileUrl'] = $action->getWebUrl();//输出的图片路径.
                    //$action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    //$action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    //$action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云,并且返回七牛云的图片地址
                    $qiniuyun = new Qiniu(\Yii::$app->params['qiniuyun']);//加载配置.
                    $key = $action->getWebUrl();//输出的图片路径
                    //上传文件到七牛云  同时指定一个key(名称,文件名)
                    $file = $action->getSavePath();//绝对路径文件名.
                    $qiniuyun->uploadFile($file,$key);
                    //获取七牛云上文件的url地址
                    $url = $qiniuyun->getLink($key);


                    $action->output['fileUrl'] = $url;//输出图片的路径
                },
            ],
            //编辑器
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
             /*   'config' => [
                    "imageUrlPrefix"  => "http://ovybghts3.bkt.clouddn.com/",//图片访问路径前缀
                    "imagePathFormat" => "/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}",//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot")
                ]*/
            ]
        ];
    }

}

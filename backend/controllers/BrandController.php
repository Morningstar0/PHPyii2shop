<?php

namespace backend\controllers;
use backend\models\Brand;
use yii\data\Pagination;
use yii\data\Sort;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

class BrandController extends \yii\web\Controller
{
    //显示品牌列表数据.
    public function actionIndex()
    {
        $sort = new Sort([
            'attributes' => ['id', 'name', 'sort','status'],
        ]);
        $brands = Brand::find();
        $pager = new Pagination([
            'totalCount' => $brands->count(),//总数据条数.
            'defaultPageSize' => 3//每页多少条.
        ]);
        //条件查询.offset为偏移量.limit为取多少条.
        $brands = $brands->limit($pager->limit)->offset($pager->offset)->orderBy($sort->orders)->where(['>','status',-1])->all();
        return $this->render('index', ['brands' => $brands,'sort' => $sort, 'pager' => $pager]);
    }
    //添加数据.
    public function actionAdd(){
        $brand = new Brand();
        $request = \Yii::$app->request;
        if ($request->isPost){
            $brand->load($request->post());
            if ($brand->validate()){
                $brand->save(false);
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['brand/index']);
            } else {
                //验证失败,获取错误信息.
                var_dump($brand->getErrors());
                exit;
            }
        }
        $brand->status = 0;
        return $this->render('add', ['model' => $brand]);
    }
    //修改数据.
    public function actionEdit($id){
        $brand = Brand::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $brand->load($request->post());
            if ($brand->validate()){
                $brand->save(false);
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['brand/index']);
            } else {
                //验证失败,获取错误信息.
                var_dump($brand->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $brand]);
    }
    //删除数据
    public function actionDelete()
    {
        $id = \Yii::$app->request->post('id');
        $model = Brand::findOne($id);
        if($model){
            $model->status = -1;
            $model->save(false);
            return 'success';
        }
        return 'fail';
    }
    //文件上传.
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload/brand',
                'baseUrl' => '@web/upload/brand',
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
        ];
    }

}

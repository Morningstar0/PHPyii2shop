<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\data\Sort;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    //显示列表数据.
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
            //实例化上传文件.
            $brand->file = UploadedFile::getInstance($brand,'file');
            if ($brand->validate()){
                $brand->saveImg();
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
            //实例化上传文件.
            $brand->file = UploadedFile::getInstance($brand,'file');
            if ($brand->validate()){
                $brand->saveImg();
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
    public function actionDelete($id)
    {
        $model = Brand::findOne($id);
        $model->status = -1;
        $model->save(false);
        if ($model->status == -1) {
            \Yii::$app->session->setFlash('success', '删除成功');
        }else{
            \Yii::$app->session->setFlash('success', '删除失败');
        }
        return $this->redirect(['brand/index']);
    }
}

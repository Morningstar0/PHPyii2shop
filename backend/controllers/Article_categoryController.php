<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\data\Sort;

class Article_categoryController extends \yii\web\Controller
{
    //显示列表数据.
    public function actionIndex()
    {
        $sort = new Sort([
            'attributes' => ['id', 'name', 'sort','status'],
        ]);
        $model = ArticleCategory::find();
        $pager = new Pagination([
            'totalCount' => $model->count(),//总数据条数.
            'defaultPageSize' => 3//每页多少条.
        ]);
        //条件查询.offset为偏移量.limit为取多少条.
        $model = $model->limit($pager->limit)->offset($pager->offset)->orderBy($sort->orders)->where(['>','status',-1])->all();
        return $this->render('index', ['model' => $model,'sort' => $sort, 'pager' => $pager]);
    }
    //添加数据.
    public function actionAdd(){
        $model = new ArticleCategory();
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->save(false);
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['article_category/index']);
            } else {
                //验证失败,获取错误信息.
                var_dump($model->getErrors());
                exit;
            }
        }
        $model->status = 0;
        return $this->render('add', ['model' => $model]);
    }
    //修改数据.
    public function actionEdit($id){
        $model = ArticleCategory::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->save(false);
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['article_category/index']);
            } else {
                //验证失败,获取错误信息.
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //删除数据
    public function actionDelete($id)
    {
        $model = ArticleCategory::findOne($id);
        $model->status = -1;
        $model->save(false);
        if ($model->status == -1) {
            \Yii::$app->session->setFlash('success', '删除成功');
        }else{
            \Yii::$app->session->setFlash('success', '删除失败');
        }
        return $this->redirect(['article_category/index']);
    }

}

<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\data\Sort;

class ArticleController extends \yii\web\Controller
{
    //显示品牌列表数据.
    public function actionIndex()
    {
        $sort = new Sort([
            'attributes' => ['id', 'name', 'sort','status'],
        ]);
        $article = Article::find();
        $pager = new Pagination([
            'totalCount' => $article->where(['>','status',-1])->count(),//总数据条数.
            'defaultPageSize' => 3//每页多少条.
        ]);
        //条件查询.offset为偏移量.limit为取多少条.
        $article = $article->limit($pager->limit)->offset($pager->offset)->orderBy($sort->orders)->where(['>','status',-1])->all();
        return $this->render('index', ['article' => $article,'sort' => $sort, 'pager' => $pager]);
    }
    //添加数据.
    public function actionAdd(){
        $article = new Article();
        $article_detail = new ArticleDetail();
        $article_category = ArticleCategory::find()->all();
        $request = \Yii::$app->request;
        if ($request->isPost){
            $article->load($request->post());
            $article_detail->load($request->post());
            if ($article->validate() && $article_detail->validate()){
                $article->create_time = time();
                $article->save();
                $article_detail->article_id = $article->id;
                $article_detail->save();
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['article/index']);
            } else {
                //验证失败,获取错误信息.
                var_dump($article->getErrors(),$article_detail->getErrors());
                exit;
            }
        }
        $article->status = 0;
        return $this->render('add', ['article' => $article,'article_detail' => $article_detail,'article_category'=>$article_category]);
    }
    //修改数据.
    public function actionEdit($id){
        $article = Article::findOne(['id'=>$id]);
        $article_detail = ArticleDetail::findOne(['article_id'=>$id]);
        $article_category = ArticleCategory::find()->all();
        $request = \Yii::$app->request;
        if ($request->isPost){
            $article->load($request->post());
            $article_detail->load($request->post());
            if ($article->validate() && $article_detail->validate()){
                $article->save();
                $article_detail->save();
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['article/index']);
            } else {
                //验证失败,获取错误信息.
                var_dump($article->getErrors(),$article_detail->getErrors());
                exit;
            }
        }
        return $this->render('add', ['article' => $article,'article_detail' => $article_detail,'article_category'=>$article_category]);
    }
    //删除数据
    public function actionDelete()
    {
        $id = \Yii::$app->request->post('id');
        $model = Article::findOne($id);
        if($model){
            $model->status = -1;
            $model->save(false);
            return 'success';
        }
        return 'fail';
    }



}

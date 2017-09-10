<?php

namespace backend\controllers;

use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\data\Sort;

class Goods_categoryController extends \yii\web\Controller
{
    //显示商品分类列表数据.
    public function actionIndex()
    {
        $sort = new Sort([
            'attributes' => ['id', 'name'],
        ]);
        $goodsCategory = GoodsCategory::find();
        $pager = new Pagination([
            'totalCount' => $goodsCategory->count(),//总数据条数.
            'defaultPageSize' => 3//每页多少条.
        ]);
        //条件查询.offset为偏移量.limit为取多少条.
        $goodsCategory = $goodsCategory->limit($pager->limit)->offset($pager->offset)->orderBy($sort->orders)->all();
        return $this->render('index', ['goodsCategory' => $goodsCategory,'sort' => $sort, 'pager' => $pager]);
    }
    //添加数据.
    public function actionAdd()
    {
        $GoodsCategory = new GoodsCategory();
        $request =  \Yii::$app->request;
        if ($request->isPost){
            $GoodsCategory->load($request->post());//加载数据.
            if ($GoodsCategory->validate()){//验证数据.
                //判断添加是顶级节点还是子级节点.
                if ($GoodsCategory->parent_id){
                    //非顶级分类(子分类)
                    $parent = GoodsCategory::findOne(['id'=>$GoodsCategory->parent_id]);
                    $GoodsCategory->prependTo($parent);
                }else{
                    //顶级分类
                    $GoodsCategory->makeRoot();
                }
               /* //生成根节点.
                $parent = new GoodsCategoryQuery(['name' => $GoodsCategory->name]);
                $parent->makeRoot();
                //生成子节点.添加在节点之前.
                $child = new GoodsCategoryQuery(['name' => 'Russia']);
                $child->prependTo($parent);
                //生成子节点.添加在节点之后.
                $child = new GoodsCategoryQuery(['name' => 'Russia']);
                $child->appendTo($parent);*/
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goods_category/index']);
            } else {
                //验证失败,获取错误信息.
                var_dump($GoodsCategory->getErrors());
                exit;
            }
        }
        return $this->render('add',['GoodsCategory'=>$GoodsCategory]);
    }
    //修改数据.
    public function actionEdit($id)
    {
        $GoodsCategory = GoodsCategory::findOne(['id'=>$id]);
        $request =  \Yii::$app->request;
        if ($request->isPost){
            $GoodsCategory->load($request->post());//加载数据.
            if ($GoodsCategory->validate()){//验证数据.
                //判断添加是顶级节点还是子级节点.
                if ($GoodsCategory->parent_id){
                    //非顶级分类(子分类)
                    $parent = GoodsCategory::findOne(['id'=>$GoodsCategory->parent_id]);
                    $GoodsCategory->prependTo($parent);
                }else{
                    //顶级分类
                    $GoodsCategory->makeRoot();
                }
                /* //生成根节点.
                 $parent = new GoodsCategoryQuery(['name' => $GoodsCategory->name]);
                 $parent->makeRoot();
                 //生成子节点.添加在节点之前.
                 $child = new GoodsCategoryQuery(['name' => 'Russia']);
                 $child->prependTo($parent);
                 //生成子节点.添加在节点之后.
                 $child = new GoodsCategoryQuery(['name' => 'Russia']);
                 $child->appendTo($parent);*/
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goods_category/index']);
            } else {
                //验证失败,获取错误信息.
                var_dump($GoodsCategory->getErrors());
                exit;
            }
        }
        return $this->render('add',['GoodsCategory'=>$GoodsCategory]);
    }
    //删除数据
    public function actionDelete($id){
        $goodsCategory = GoodsCategory::findOne(['id'=>$id]);
        $result = GoodsCategory::find()->where(['>','lft',$goodsCategory->lft])->where(['<','rgt',$goodsCategory->rgt])->all();
        //var_dump($result);exit;
        if ($result){
            \Yii::$app->session->setFlash('warning','该类下有子类不能删除!!!');
        }else{
            \Yii::$app->session->setFlash('success','删除成功');
            GoodsCategory::findOne(['id'=>$id])->delete();
        }
        return $this->redirect(['goods_category/index']);
    }





}

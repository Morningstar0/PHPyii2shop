<?php
$from = \yii\bootstrap\ActiveForm::begin();
echo $from->field($GoodsCategory,'name')->textInput();
echo $from->field($GoodsCategory,'parent_id')->hiddenInput();
echo '<div><ul id="treeDemo" class="ztree"></ul></div>';//zTree.树状.分类显示.
echo $from->field($GoodsCategory,'intro')->textInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
/**
 * @var $this \yii\web\View
 *
 */
//注册zTree的JS与CSS.
$this->registerCssFile('@web/ztree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/ztree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
$goodsCategories = json_encode(\backend\models\GoodsCategory::getZNodes());
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
       var zTreeObj;
       //zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
       var setting = {
           data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
		        }
		   },     
           callback: {//事件回调函数
                    onClick: function( event, treeId, treeNode) {
                     console.log(treeNode);
		             //获取当前点击节点的id,写入parent_id的值
		             $("#goodscategory-parent_id").val(treeNode.id);
                    }
           },
           view: {
		         showIcon: false
	        }
       };
       // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
       var zNodes = {$goodsCategories};
       zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        //展开全部节点
       zTreeObj.expandAll(true);
        //修改 根据当前分类的parent_id来选中节点
        //获取你需要选中的节点 
        var node = zTreeObj.getNodeByParam("id", "{$GoodsCategory->parent_id}", null);
        zTreeObj.selectNode(node);
JS
));





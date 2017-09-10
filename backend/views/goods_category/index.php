<?php
/* @var $this yii\web\View */
echo $sort->link('id') . ' | ' . $sort->link('name');
?><br/>
    <a href="<?=\yii\helpers\Url::to(['goods_category/add'])?>"  class="btn btn-primary">添加商品分类</a>
    <table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>简介</th>
            <th>操作</th>
        </tr>
        <?php foreach ($goodsCategory as $row):?>
            <tr>
                <td><?=$row->id?></td>
                <td><?=$row->name?></td>
                <td><?=$row->intro?></td>
                <td>
                    <a href="<?=\yii\helpers\Url::to(['goods_category/edit','id'=>$row->id])?>" class="btn btn-default">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                    <a href="<?=\yii\helpers\Url::to(['goods_category/delete','id'=>$row->id])?>" class="btn btn-default del_btn">
                        <span class="glyphicon glyphicon-trash"></span>
                    </a>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);

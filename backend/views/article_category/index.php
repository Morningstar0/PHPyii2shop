<?php
/* @var $this yii\web\View */
echo $sort->link('id') . ' | ' . $sort->link('name') . ' | ' . $sort->link('sort') . ' | ' . $sort->link('status');
?><br/>
    <a href="<?=\yii\helpers\Url::to(['article_category/add'])?>"  class="btn btn-primary">添加文章分类</a>
    <table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>排序</th>
            <th>状态</th>
            <th>简介</th>
            <th>操作</th>
        </tr>
        <?php foreach ($model as $row):?>
            <tr>
                <td><?=$row->id?></td>
                <td><?=$row->name?></td>
                <td><?=$row->sort?></td>
                <td><?=$row->status==0?'隐藏':'正常'?></td>
                <td><?=$row->intro?></td>
                <td>
                    <a href="<?=\yii\helpers\Url::to(['article_category/edit','id'=>$row->id])?>" class="glyphicon glyphicon-pencil"></a>
                    <a href="<?=\yii\helpers\Url::to(['article_category/delete','id'=>$row->id])?>" class="glyphicon glyphicon-trash"></a>
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

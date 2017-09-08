<?php
/* @var $this yii\web\View */
echo $sort->link('id') . ' | ' . $sort->link('name') . ' | ' . $sort->link('sort') . ' | ' . $sort->link('status');
?><br/>
    <a href="<?=\yii\helpers\Url::to(['brand/add'])?>"  class="btn btn-primary">添加品牌</a>
    <table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>排序</th>
            <th>状态</th>
            <th>简介</th>
            <th>LOGO</th>
            <th>操作</th>
        </tr>
        <?php foreach ($brands as $row):?>
            <tr data_id="<?=$row->id?>">
                <td><?=$row->id?></td>
                <td><?=$row->name?></td>
                <td><?=$row->sort?></td>
                <td><?=$row->status==0?'隐藏':'正常'?></td>
                <td><?=$row->intro?></td>
                <td>
                    <img src="<?=$row->logo?>" class="img-circle" style="width: 80px"/>
                </td>
                <td>
                    <a href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$row->id])?>" class="btn btn-default">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                    <a href="javascript:;" class="btn btn-default del_btn">
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
//注册JS代码.
$del_url = \yii\helpers\Url::to(['brand/delete']);
    $this->registerJs(new \yii\web\JsExpression(
            <<<JS
        $(".del_btn").click(function() {
            if (confirm("确认删除?")){
                var tr = $(this).closest('tr');
                var id = tr.attr("data_id");
            $.post("{$del_url}",{id:id},function(data){
                if(data == 'success'){
                    alert('删除成功');
                    tr.hide('slow');
                }else{
                    alert('删除失败');
                }
            });
            }
        })
JS
    ));

<?php
/* @var $this yii\web\View */
echo $sort->link('id') . ' | ' . $sort->link('name') . ' | ' . $sort->link('sn') . ' | ' . $sort->link('stock');
?><br/>
    <a href="<?=\yii\helpers\Url::to(['goods/add'])?>"  class="btn btn-primary">添加商品</a>
    <table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
            <th>货号</th>
            <th>名称</th>
            <th>价格</th>
            <th>库存</th>
            <th>LOGO</th>
            <th>操作</th>
        </tr>
        <?php foreach ($goods as $row):?>
            <tr data_id="<?=$row->id?>">
                <td><?=$row->id?></td>
                <td><?=$row->sn?></td>
                <td><?=$row->name?></td>
                <td><?=$row->shop_price?></td>
                <td><?=$row->stock?></td>
                <td>
                    <img src="<?=$row->logo?>" class="img-circle" style="width: 80px"/>
                </td>
                <td>
                    <a href="<?=\yii\helpers\Url::to(['goods/show','id'=>$row->id])?>" class="btn btn-info ">
                        <span class="glyphicon glyphicon-eye-open">查看</span>
                    </a>
                    <a href="<?=\yii\helpers\Url::to(['goods/edit','id'=>$row->id])?>" class="btn btn-warning ">
                        <span class="glyphicon glyphicon-pencil">修改</span>
                    </a>
                    <a href="javascript:;" class="btn btn-danger del_btn">
                        <span class="glyphicon glyphicon-trash">删除</span>
                    </a>
                    <a href="<?=\yii\helpers\Url::to(['goods/album','id'=>$row->id])?>" class="btn btn-default ">
                        <span class="glyphicon glyphicon-picture ">相册</span>
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
$del_url = \yii\helpers\Url::to(['goods/delete']);
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


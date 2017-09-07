<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'sort')->input('number');
echo $form->field($model,'intro')->textarea(['rows'=>5]);
echo $form->field($model,'status',['inline'=>true])->radioList(['0'=>'隐藏','1'=>'正常'],['class'=>'label-group']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//表单结束.
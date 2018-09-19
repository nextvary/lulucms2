<?php

use yii\helpers\Html;
use source\core\widgets\ActiveForm;
use source\models\Menu;
use source\libs\Common;
use source\libs\Constants;
use source\libs\TreeHelper;
use yii\helpers\ArrayHelper;
use source\modules\rbac\models\Role;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 255,'readonly'=>$model->isNewRecord?null:'readonly']) ?>

    <?= $form->field($model, 'password')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'role')->dropDownList(ArrayHelper::map($this->rbacService->getAllRoles(), 'id', 'name','category')) ?>
    
    <?= $form->field($model, 'status')->radioList(Constants::getStatusItems()) ?>
    <?= $form->field($model, 'type')->radioList(Constants::getPermissionType()) ?>

    <div id="tabs-controller" class="">

    </div>




    <?= $form->defaultButtons() ?>

    <?php ActiveForm::end(); ?>
<script>
    $(function () {
        var permission=('<?=$model->permission ?>');
        permission=permission?JSON.parse(permission):'';
        var type=$('input[name="User[type]"]:checked').val();
        if(type==2){
            getPermission(permission);
        }else{
            $('#tabs-controller').html('');
        }
        $('input[name="User[type]"]').change(function (e) {
            if($(this).val()==2){
                getPermission(permission);
            }else{
                $('#tabs-controller').html('');

            }
        });
        $('#user-role ').change(function () {
            if(type==2){
                getPermission(permission);
            }else{
                $('#tabs-controller').html('');
            }
        });
    });
    function getPermission(permission) {
        var role=$('#user-role').val();
        var url="<?=\yii\helpers\Url::to(['/user/user/create','role'=>'']) ?>"+role;
        $.ajax({
            "url":url,
            'type':'get',
            'dataType':'json',
            'success':function (item) {
                createHtml(item,permission);
            }
        })
    }
    function createHtml(item,permission) {
        var html='';
        var li='';
        var value='';
        for(var i=0;i<item.length;i++){
            li='';

            for(var j=0;j<item[i].default_value.length;j++){
                value =item[i].default_value[j].split('|');
                li+='<li><label><input type="checkbox" attrname="'+item[i].permission+'" name="Permission['+item[i].permission+'][]" <?=$model->isNewRecord?'checked':'' ?> value="'+value[0]+'" >'+value[1]+'</label></li>';
            }

            html+='<div class="da-form-row">'+
                '<label for="permission-'+item[i].permission+'">'+item[i].name+'</label>'+
                '<div class="da-form-item small">'+
                '<ul class="da-form-list inline">'+
                li+
                '</ul>'+
                '</div>'+
                '<div class="help-block"></div>'+
            '</div>';
        }
        $('#tabs-controller').html(html);

        if(permission!=''){
            $('#tabs-controller input').each(function (e,item) {
                var name=$(item).attr('attrname');
                for( i in permission){
                    if(i==name){
                       for(j in permission[i]){
                           if($(item).val() == permission[i][j] ){
                               $(item).attr('checked','checked');
                           }
                        }


                    }
                }
            })
        }


    }

</script>

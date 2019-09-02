<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="alert alert-success">
        Форма была отправлена
    </div>
    <div class="body-content">

        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model,'auto') ?>
        <?= $form->field($model,'model') ?>
        <?= $form->field($model,'number') ?>
        <?= $form->field($model,'color')->dropdownList($colors) ?>
        <?= $form->field($model,'parking')->checkbox() ?>
        <?= $form->field($model,'comment')->textarea() ?>
        <div class="form-group">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>

        <?php
        $js = <<<JS
    $('form').on('beforeSubmit', function(){
        var data = $(this).serialize();
        $.ajax({
            url: '/',
            type: 'POST',
            data: data,
            success: function(res){
                if(res.code == 0){
                    $(".alert").show();
                    setTimeout(function(){
                        $(".alert").hide();
                    },2000);
                    var items = res.data.items;
                    if(items.length !=0){
                        for(var i = 0;i < items.length; i++){
                           $(".table").append("<tr><td>"+items[i].id+"</td><td>"+items[i].auto+"</td><td>"+items[i].model+"</td><td>"+items[i].number+"</td><td>"+items[i].color+"</td><td>"+items[i].parking+"</td><td>"+items[i].comment+"</td></tr>");
                        }                    
                    }
                }
            },
            error: function(){
                alert('Error!');
            }
        });
        return false;
    });
    $("body").on('click','#refreshTable',function(){
        $.ajax({
            url: 'site/cars',
            type: 'POST',
            datatype: "json",
            success: function(res){
                var items = res.items;
                if(items.length !=0){
                    for(var i = 0;i < items.length; i++){
                       $(".table").append("<tr><td>"+items[i].id+"</td><td>"+items[i].auto+"</td><td>"+items[i].model+"</td><td>"+items[i].number+"</td><td>"+items[i].color+"</td><td>"+items[i].parking+"</td><td>"+items[i].comment+"</td></tr>");
                    }                    
                }
            },
            error: function(){
                alert('Error!');
            }
        });
        return false;
    });
JS;

        $this->registerJs($js);
        ?>

        <div class="forTableBlock">
            <table class="table">
            <tr>
                <td scope="col">ID</td>
                <td scope="col">Марка авто</td>
                <td scope="col">Модель</td>
                <td scope="col">Номер</td>
                <td scope="col">Цвет</td>
                <td scope="col">Парковка оплачена</td>
                <td scope="col">Комментарий</td>
            </tr>
            <?php
            if(!empty($items)) {
                foreach($items as $item){
                    echo "<tr>";
                        echo "<td scope='row'>".$item['id']."</td>";
                        echo "<td>".$item['auto']."</td>";
                        echo "<td>".$item['model']."</td>";
                        echo "<td>".$item['number']."</td>";
                        echo "<td>".$item['color']."</td>";
                        echo "<td>";
                            echo $item['parking'];
                        echo "</td>";
                        echo "<td>".$item['comment']."</td>";
                    echo "</tr>";
                }
            }

            ?>
        </table>

        </div>
        <?= Html::button("Обновить таблицу",["id" => "refreshTable", "class" => 'btn btn-primary']); ?>
    </div>
</div>

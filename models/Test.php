<?php

namespace app\models;


use yii\base\Model;
use yii\db\ActiveRecord;

class Test extends ActiveRecord {
//    public $auto;
//    public $model;
//    public $number;
//    public $color;
//    public $parking;
//    public $comment;

    public function attributeLabels()
    {
        return [
            'auto' => "Марка авто",
            'model' => "Модель авто",
            'number' => "Номер авто",
            'color' => "Цвет авто",
            'parking' => "Парковка оплачена",
            'comment' => "Комментарий",
        ];
    }

    public function rules()
    {
        return [
            [['auto','model','number','color'],'required'],
            ['number', 'match', 'pattern' => '/^[а-я]\d{3}[а-я]{2}\d{2,3}$/iu',"message" => "Номер невалидный"],
//            ['number', 'when' => function($model){ return !preg_match("/^[АаВвЕеКкМмНнОоРрСсТтУуХх]\d{3}[АаВвЕеКкМмНнОоРрСсТтУуХх]{2}\d{2,3}$/",$model->number); },"message" => "Номер невалидный"],
            [['parking','comment'], 'safe']
        ];
    }


}
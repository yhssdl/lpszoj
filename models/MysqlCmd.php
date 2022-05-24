<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "{{%mysql}}".
 *
 * @property int $id
 * @property string $command
 * @property string $description
 * @property string $alt_msg

 */
class MysqlCmd extends \yii\db\ActiveRecord
{

    /**
    * @inheritdoc
    */
    public static function tableName()
    {
    return '{{%mysql}}';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['command', 'description','alt_msg'], 'string'],
            [['id'], 'integer'],
        ];
    }
 

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'command' => Yii::t('app', 'Command'),
            'description' => Yii::t('app', 'Description'),
            'alt_msg' => Yii::t('app', 'Message'),
        ];
    }

}

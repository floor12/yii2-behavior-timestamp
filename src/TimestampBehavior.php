<?php

namespace floor12\timestamp;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use common\models\User;
use yii\validators\Validator;
use yii;

class TimestampBehavior extends Behavior
{

    static public function attributeLabels()
    {
        return [
            'created' => 'Создано',
            'updated' => 'Обновлено',
            'create_user_id' => 'Создал',
            'update_user_id' => 'Обновил',
            'creator' => 'Создано',
            'updator' => 'Обновлено',
        ];
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
        ];
    }

    public function beforeValidate()
    {
        if (isset(Yii::$app->user))
            $this->owner->create_user_id = Yii::$app->user->id;
        $this->owner->updated = date("Y-m-d H:i:s");
    }

    public function beforeInsert()
    {
        if (isset(Yii::$app->user))
            $this->owner->create_user_id = Yii::$app->user->id;
    }

    public function getCreator()
    {
        return User::find($this->owner->create_user_id);
    }

    public function getUpdator()
    {
        return User::find($this->owner->update_user_id);
    }

    public function attach($owner)
    {
        parent::attach($owner);
        $validators = $owner->validators;
        $validatorInt = Validator::createValidator('integer', $owner, ['create_user_id', 'update_user_id']);
        $validatorSafe = Validator::createValidator('safe', $owner, ['created', 'updated']);
        $validators->append($validatorInt);
        $validators->append($validatorSafe);
    }

}

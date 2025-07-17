<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class WsConnection extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%ws_connections}}';
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
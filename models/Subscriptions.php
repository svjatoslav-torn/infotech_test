<?php

namespace app\models;

use Yii;

class Subscriptions extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'subscriptions';
    }

    public function rules(): array
    {
        return [
            ['id_author', 'required', 'message' => 'айди автора обязательно'],
            ['phone', 'required', 'message' => 'телефон обязательно'],
            ['id_author', 'integer'],
            ['phone', 'string'],
        ];
    }

    /**
     * Получить все подписки автора
     * @param int $id
     * @return Subscriptions[]
     */
    public static function findByAuthor(int $id)
    {
        return static::findAll(['id_author' => $id]);
    }
}

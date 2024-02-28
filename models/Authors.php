<?php

namespace app\models;

/**
 * Модель авторов
 */
class Authors extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'authors';
    }

    public function rules(): array
    {
        return [
            ['full_name', 'required', 'message' => 'ФИО автора является обязательным параметром.'],
            [['full_name'], 'string'],
        ];
    }

    public static function find()
    {
        return new AuthorQuery(get_called_class());
    }

    public static function findAuthor($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Получить Киниги, связанные данные м-м
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getBooks()
    {
        return $this->hasMany(Books::class, ['id' => 'id_author'])
            ->viaTable('books_authors', ['id_book' => 'id']);
    }

}
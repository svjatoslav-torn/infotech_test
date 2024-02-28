<?php

namespace app\models\resource;

use Yii;
use app\models\Books as ModelBooks;

/**
 * Ресурс книг для ответов
 */
class Books extends ModelBooks
{
    public function fields()
    {
        return [
            'id',
            'name',
            'publication_year',
            'description',
            'isbn',
            'img_path' => function () {
                if ($this->img_path !== null && strlen($this->img_path) > 1) {
                    return Yii::$app->request->hostname . '/' . $this->img_path;
                }
                return null;
            },
        ];
    }

    public function extraFields()
    {
        return ['authors'];
    }
}

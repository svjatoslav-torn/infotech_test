<?php
namespace app\models\resource;

use app\models\Authors as ModelAuthor;

/**
 * Ресурс для отдачи авторов
 */
class Authors extends ModelAuthor
{
    public function fields()
    {
        return [
            'id',
            'full_name',
        ];
    }

    public function extraFields()
    {
        return ['books'];
    }
}

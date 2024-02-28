<?php
namespace app\models\forms;

use yii\base\Model;

/**
 * Форма подписки
 */
class SubscribesForm extends Model
{
    public ?int $id_author = null;
    public ?string $phone = null;


    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['id_author', 'required', 'message' => 'айди автора обязательно'],
            ['phone', 'required', 'message' => 'телефон обязательно'],
            ['id_author', 'integer'],
            ['phone', 'string'],
        ];
    }
}

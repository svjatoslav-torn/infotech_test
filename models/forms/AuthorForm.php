<?php
namespace app\models\forms;

use yii\base\Model;

/**
 * Форма круда автора
 */
class AuthorForm extends Model
{
    public ?string $full_name = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['full_name', 'required', 'message' => 'ФИО автора является обязательным параметром.'],
            ['full_name', 'string'],
        ];
    }
}
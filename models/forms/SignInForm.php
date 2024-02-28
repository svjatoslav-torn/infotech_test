<?php
namespace app\models\forms;

use yii\base\Model;

/**
 * Форма логина
 */
class SignInForm extends Model
{
    public string $email;
    public string $password;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['email', 'required', 'message' => 'Пожалуйста отправьте свой Email'],
            ['password', 'required', 'message' => 'Пожалуйста отправьте пароль'],
            ['email', 'email', 'message' => 'Введите нормальную валидную почту'],
        ];
    }
}

<?php
namespace app\models\forms;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use app\models\User;

/**
 * Форма для регистрации
 */
class SignUpForm extends Model
{
    public string $name;
    public string $email;
    public string $password;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['name', 'required', 'message' => 'Поле "Имя" обязательно для заполнения'],
            ['email', 'required', 'message' => 'Поле "Email" обязательно для заполнения'],
            ['password', 'required', 'message' => 'Поле "Пароль" обязательно для заполнения'],
            ['name', 'string', 'max' => 120],
            ['email', 'email', 'message' => 'Адрес электронной почты в формате sefkiss.torn@yandex.ru'],
            ['password', 'string', 'min' => 8],
        ];
    }
}

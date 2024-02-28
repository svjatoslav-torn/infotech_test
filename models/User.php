<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $token
 * @property string $token_expire
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const DEFAULT_EXPIRE_AUTH_TOKEN = 60 * 5;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'users';
    }

    public function rules(): array
    {
        return [
            [['name', 'email', 'password'], 'required'],
            ['name', 'string', 'max' => 120],
            ['email', 'email'],
            ['email', 'unique', 'message' => 'Пользователь с таким Email уже зарегистрирован'],
            [['token'], 'safe'],
            ['password', 'string'],
        ];
    }

    public function __construct(array $attributes = [])
    {
        if (!empty($attributes)) {
            $attributes['password'] = $this->generateHashOfPassword($attributes['password'] ?? '');
            $this->setAttributes($attributes, false);
        }
    }

    /**
     *  Получение юзера по id
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     *  Ауф по токену, с помощью BearerAuth
     *
     * @param string $token
     * @param string|null $type
     *
     * @return Token
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->where(['token' => $token])
            ->andWhere(['>', 'token_expire', time()])
            ->one();
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByemail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->token;
    }

    public function validateAuthKey($authKey)
    {
        return $this->token === $authKey;
    }

    /**
     * Создаем хеш пароля
     * @param string $password
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateHashOfPassword(string $password): string
    {
        return $this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function generateAuthToken()
    {
        $this->token = Yii::$app->security->generateRandomString();
        $this->token_expire = time() + Yii::$app->params['defaultExpireAuthToken'] ?? self::DEFAULT_EXPIRE_AUTH_TOKEN;
        $this->save();
    }

    /**
     * Validates password
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
}

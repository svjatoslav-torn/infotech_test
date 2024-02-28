<?php
namespace app\controllers\api\v1;

use Yii;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\forms\SignInForm;
use app\models\forms\SignUpForm;
use app\models\User;

/**
 * Класс для авторизации и регистрации пользователей
 *
 * @package app\controllers
 * @since 1.0.0.0
 */
class AuthController extends Controller
{
    /**
     * Поведения
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'signup' => ['post'],
                    'signin' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->enableCsrfValidation = false;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return true;
    }

    /**
     * Регистрация
     *
     * @return Response|array|string
     */
    public function actionSignup(): Response|array|string
    {
        $form = new SignUpForm();
        $form->load(Yii::$app->request->post(), '');

        if (!$form->validate()) {
            Yii::$app->response->statusCode = 400;
            return $form->errors;
        }

        $user = new User([
            'name' => $form->name,
            'email' => $form->email,
            'password' => $form->password,
        ]);

        if (!$user->save()) {
            Yii::$app->response->statusCode = 400;
            return $user->getErrors();
        }

        Yii::$app->response->statusCode = 201;
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    /**
     * Авторизация
     *
     * @return Response|string
     */
    public function actionSignin()
    {
        $form = new SignInForm();
        $form->load(Yii::$app->request->post(), '');

        if (!$form->validate()) {
            Yii::$app->response->statusCode = 400;
            return $form->errors;
        }

        $user = User::findByemail($form->email);
        if (!$user || !$user->validatePassword($form->password)) {
            Yii::$app->response->statusCode = 400;
            $form->addError('email', 'Не верный логин. Проверьте учетные данные!');
            $form->addError('password', 'Не верный пароль. Проверьте учетные данные!');
            return $form->errors;
        }

        $user->generateAuthToken();

        return [
            'token' => $user->token,
            'expired' => date(DATE_RFC3339, $user->token_expire),
            'user_id' => $user->id,
        ];
    }
}

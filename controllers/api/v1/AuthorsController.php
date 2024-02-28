<?php
namespace app\controllers\api\v1;

use app\models\forms\AuthorForm;
use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use app\models\forms\PostForm;
use app\models\resource\Authors;

class AuthorsController extends ActiveController
{
    public $modelClass = Authors::class;

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'optional' => ['index', 'view'],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'create', 'update', 'delete'],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * Переопределение дефолтных рест экшенов
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update']);
        return $actions;
    }

    /**
     * Обновление автора
     * @param int $id
     * @return array
     */
    public function actionUpdate(int $id)
    {
        $author = Authors::findAuthor($id);

        $form = new AuthorForm();
        $form->load(Yii::$app->request->bodyParams, '');
        if (!$form->validate()) {
            Yii::$app->response->statusCode = 400;
            return $form->errors;
        }

        $author->full_name = $form->full_name;

        if (!$author->save()) {
            Yii::$app->response->statusCode = 400;
            return $author->getErrors();
        }

        Yii::$app->response->statusCode = 201;
        return $author->getAttributes();
    }
}

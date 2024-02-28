<?php

namespace app\controllers\api\v1;

use Yii;
use app\models\forms\SubscribesForm;
use app\models\Subscriptions;
use yii\rest\Controller;

class SubscriptionsController extends Controller
{
    /**
     * Создание подписки
     * @return array|string[]
     */
    public function actionCreate()
    {
        $form = new SubscribesForm();
        $form->load(Yii::$app->request->bodyParams, '');
        if (!$form->validate()) {
            Yii::$app->response->statusCode = 400;
            return $form->errors;
        }

        if (Subscriptions::find()->where("id_author = $form->id_author AND phone = $form->phone")->one()) {
            Yii::$app->response->statusCode = 200;
            return [
                'description' => 'Вы уже формляли подписку на автора.',
            ];
        }

        $sb = new Subscriptions();
        $sb->setAttributes($form->getAttributes());

        if (!$sb->save()) {
            Yii::$app->response->statusCode = 400;
            return $sb->getErrors();
        }

        Yii::$app->response->statusCode = 201;
        return [
            'description' => 'Подписка успешно оформлена',
            ...$form->getAttributes(),
        ];
    }
}
<?php
namespace app\controllers\api\v1;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use app\models\forms\BookForm;
use app\models\resource\Books;

class BooksController extends ActiveController
{
    public $modelClass = Books::class;

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

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['create']);
        return $actions;
    }

    /**
     * Создание книги
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $form = new BookForm(['scenario' => BookForm::SCENARIO_CREATE_BOOK]);
        $form->load(Yii::$app->request->bodyParams, '');
        if (!$form->validate()) {
            Yii::$app->response->statusCode = 400;
            return $form->errors;
        }

        $book = Books::newBook($form);
        $tr = Yii::$app->db->beginTransaction();

        if (
            $book->hasErrors()
            || !$book->save()
            || \is_null($authors = $book->jsonDecode('authors', $form->authors))
        ) {
            $tr->rollBack();
            Yii::$app->response->statusCode = 400;
            return $book->getErrors();
        }

        $values = implode(',', array_map(fn($v) => "($book->id, $v)", $authors));
        $sql = <<<SQL
            INSERT INTO `books_authors` (`id_book`, `id_author`) VALUES $values
        SQL;
        Yii::$app->db->createCommand($sql)->execute();

        // Начать рассылку. Мм чтобы рассылка прошла асинхронно, запилить шедулер и запускать цмдшку
        $command = Yii::$app->basePath . DIRECTORY_SEPARATOR . "yii sms-sender $form->authors $book->name";
        $file = popen("start /B ". $command, "wb");
        if ($file) {
            pclose($file);
        }

        $tr->commit();
        Yii::$app->response->statusCode = 201;
        return $book->getAttributes();
    }

    /**
     * Обновление книги
     * @param int $id
     * @return array|string[]
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionUpdate(int $id)
    {
        $form = new BookForm();
        $form->load(Yii::$app->request->bodyParams, '');
        if (!$form->validate()) {
            Yii::$app->response->statusCode = 400;
            return $form->errors;
        }

        if (!$book = Books::findBook($id)) {
            Yii::$app->response->statusCode = 404;
            return ["error" => "Endpoint not found"];
        }

        $book->setAttributes(array_filter($form->getAttributes(), static fn($v) => !\is_null($v)), false);
        if (!empty($form->img_base64)) {
            !empty($book->img_path) && $book->deleteFile();
            $book->saveImage($book->img_path);
        }

        $tr = Yii::$app->db->beginTransaction();
        if ($book->hasErrors() || !$book->save()) {
            $tr->rollBack();
            Yii::$app->response->statusCode = 400;
            return $book->getErrors();
        }

        if (!empty($form->authors) && !\is_null($authors = $book->jsonDecode('authors', $form->authors))) {
            $values = implode(',', array_map(fn($v) => "VALUES ($book->id, $v)", $authors));
            $sql = <<<SQL
                DELETE FROM books_authors WHERE id_book = $book->id";
                INSERT INTO books_authors (id_book, id_author)
                    $values;
            SQL;
            Yii::$app->db->createCommand($sql)->execute();
        }

        $tr->commit();
        Yii::$app->response->statusCode = 201;
        return $book->getAttributes();
    }
}

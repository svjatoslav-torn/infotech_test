<?php

namespace app\models;

use app\models\forms\BookForm;
use Yii;


class Books extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE_BOOK = 'create_book';

    public ?string $img_base64 = null;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'books';
    }

    /**
     * Правила валидации
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            ['name', 'required', 'on' => self::SCENARIO_CREATE_BOOK, 'message' => 'Название книги обязательно.'],
            ['publication_year', 'required', 'on' => self::SCENARIO_CREATE_BOOK, 'message' => 'Год издания обязательно.'],
            ['isbn', 'required', 'on' => self::SCENARIO_CREATE_BOOK, 'message' => 'ISBN обязательно'],
            ['img_path', 'required', 'on' => self::SCENARIO_CREATE_BOOK, 'message' => 'Фото обложки обязательно'],
            [['name', 'description', 'isbn', 'img_path'], 'string'],
            [['publication_year'], 'integer'],
            [['description'], 'safe'],
        ];
    }

    /**
     * Создание новой книги
     * @param BookForm $form
     * @return Books
     */
    public static function newBook(BookForm $form): Books
    {
        $book = new Books(['scenario' => BookForm::SCENARIO_CREATE_BOOK]);
        $book->setAttributes($form->getAttributes(), false);
        !empty($form->img_base64) && $book->saveImage($form->img_base64);
        return $book;
    }

    /**
     * Сохранение картинки
     * @param string $stringOfFile
     * @return bool
     * @throws \yii\base\Exception
     */
    public function saveImage(string $stringOfFile): bool
    {
        $pattern = '/data:image\/(.+);base64,(.*)/';
        preg_match($pattern, $stringOfFile, $matches);
        if (count($matches) != 3) {
            $this->img_path = null;
            return false;
        }
        $pathToDB = 'images/books/' . Yii::$app->security->generateRandomString(20) . "." . $matches[1];
        $this->img_path = !@file_put_contents($this->getFullPathToFile($pathToDB), base64_decode($matches[2])) ? null : $pathToDB;
        \is_null($this->img_path) && $this->addError("img_base64", "Проблемки с файлом") ;
        return (bool) $this->img_path;
    }

    /**
     * Удаление картинки
     * @param string|null $shortPath
     * @return bool
     */
    public function deleteFile(?string $shortPath): bool
    {
        return @unlink($this->getFullPathToFile($shortPath ?? $this->img_path ?? ''));
    }

    /**
     * Получить полный путь до локально лежащего файла
     * @param string $shortPath
     * @return string
     */
    public function getFullPathToFile(string $shortPath): string
    {
        return Yii::$app->basePath . '/web/' . $shortPath;
    }

    /**
     * Декод json с проверкой на ошибки, мало ли че не валидное там было
     * @param string $attr
     * @param string $json
     * @return array|null
     */
    public function jsonDecode(string $attr, string $json): ?array
    {
        $decoded = @json_decode($json, true);
        json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) && $this->addError($attr, "Invalid format authors");
        return $decoded;
    }

    public static function find()
    {
        return new BooksQuery(get_called_class());
    }

    public static function findBook($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Получить авторов м-м
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getAuthors()
    {
        return $this->hasMany(Authors::class, ['id' => 'id_book'])
            ->viaTable('books_authors', ['id_author' => 'id']);
    }
}

<?php
namespace app\models\forms;

use yii\base\Model;

class BookForm extends Model
{
    const SCENARIO_CREATE_BOOK = 'create_book';

    public ?string $name = null;
    public ?string $img_base64 = null;
    public ?string $isbn = null;
    public ?string $description = null;
    public ?int $publication_year = null;
    public null|array|string $authors = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['name', 'required', 'on' => self::SCENARIO_CREATE_BOOK, 'message' => 'Название книги обязательно.'],
            ['publication_year', 'required', 'on' => self::SCENARIO_CREATE_BOOK, 'message' => 'Год издания обязательно.'],
            ['isbn', 'required', 'on' => self::SCENARIO_CREATE_BOOK, 'message' => 'ISBN обязательно'],
            ['img_base64', 'required', 'on' => self::SCENARIO_CREATE_BOOK, 'message' => 'Фото обложки обязательно'],
            ['authors', 'required', 'on' => self::SCENARIO_CREATE_BOOK, 'message' => 'Авторы книги обязательны'],
            [['name', 'description', 'isbn', 'img_base64', 'authors'], 'string'],
            [['publication_year'], 'integer'],
            [['description'], 'safe'],
        ];
    }
}

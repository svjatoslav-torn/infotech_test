# Ревью
В репе есть коллекция запросов постман - infotechTest.postman_collection.json
PHP 8.1 
Mysql 8.0.28
Выполнил все пункты (из-за нехватки времени опустил некоторые проверки).
СМСки шлются асинхронно.

## Постановка
Необходимо сделать на фреймворке Yii2 + MySQL каталог книг. Книга может иметь несколько авторов.

1. Книга - название, год выпуска, описание, isbn, фото главной страницы.
2. Авторы - ФИО.

Права на доступ:
1. Гость - только просмотр + подписка на новые книги автора.
2. Юзер - просмотр, добавление, редактирование, удаление.

Отчет - ТОП 10 авторов выпуствиших больше книг за какой-то год.

ПЛЮСОМ БУДЕТ
Уведомление о поступлении книг из подписки должно отправляться на смс гостю.

https://smspilot.ru/
там "Для тестирования можно использовать ключ эмулятор (реальной отправки SMS не происходит)."

## Вопросы
Чет не понятно визуальный интерфейс нужен или нет, сделаю API

## Чек лист
1. Накидать структуру БД
    * users
      * id
      * name
      * email
      * password
    * subscriptions
      * id
      * phone
      * id_author
    * books
      * id
      * name
      * publication_year
      * description
      * isbn
      * img_path
    * authors
      * id
      * full_name
    * books_authors
      * id_book
      * id_author
2. Накидать структуру АПИ
    * Авторизация и регистрация
      * POST: /api/v1/auth/signin
      * POST: /api/v1/auth/signup
    * Авторы:
      * GET /api/v1/authors - получение листа
      * GET /api/v1/authors/:id - получение автора
      * POST /api/v1/authors - создать автора
      * PUT /api/v1/authors/:id - изменение автора
      * DELETE /api/v1/authors/:id - удаление автора
    * Книги
      * GET /api/v1/books - получение листа
      * GET /api/v1/books/:id - получение книги
      * POST /api/v1/books - создать книги
      * PUT /api/v1/books/:id - изменение книги
      * DELETE /api/v1/books/:id - удаление книги
    * Подписка
      * POST /api/v1/subscriptions - создание подписки на автора
    * Отчеты
      * GET /api/v1/reports/topAuthors?year=...
3. Написать Миграцию БД
4. Авторизация
5. Cоздание дефолтного юзера - из консоли
6. Сидер поставочных данных (не успеваю, сорян)
7. Реализация ендпоинтов АПИ
8. Прикрутить АПИшку для смсок
9. Прибраться, прокоментироваться, подбить коллекцию Postman
10. Написать инструкцию если вдруг будут разворачивать
<?php

use yii\db\Migration;

/**
 * Class m240227_172223_1_0_0_0
 */
class m240227_172223_1_0_0_0 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'name' => $this->string(120)->notNull(),
            'email' => $this->string(100)->unique()->notNull(),
            'password' => $this->string()->notNull(),
        ]);
        echo "Table Users successful created.\n";

        $this->createTable('books', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'publication_year' => $this->smallInteger()->unsigned()->notNull(),
            'description' => $this->text(),
            'isbn' => $this->string(30)->notNull(),
            'img_path' => $this->string(100),
        ]);
        echo "Table Books successful created.\n";

        $this->createTable('authors', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string(255)->notNull(),
        ]);
        echo "Table Authors successful created.\n";

        $this->createTable('books_authors', [
            'id_book' => $this->integer(11)->notNull(),
            'id_author' => $this->integer(11)->notNull(),
            'PRIMARY KEY(id_book, id_author)',
        ]);
        echo "Table Books-Authors many-to-many successful created.\n";

        $this->addForeignKey(
            'fk__books_authors_id_book__books_id',
            'books_authors',
            'id_book',
            'books',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk__books_authors_id_author__authors_id',
            'books_authors',
            'id_author',
            'authors',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('subscriptions', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(20)->notNull(),
            'id_author' => $this->integer(11)->notNull(),
        ]);
        echo "Table Subscriptions successful created.\n";

        $this->addForeignKey(
            'fk__subscriptions_id_author__authors_id',
            'subscriptions',
            'id_author',
            'authors',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk__subscriptions_id_author__authors_id', 'subscriptions');

        $this->dropTable('subscriptions');
        echo "Table Subscriptions successful deleted.\n";

        $this->dropForeignKey('fk__books_authors_id_author__authors_id', 'books_authors');
        $this->dropForeignKey('fk__books_authors_id_book__books_id', 'books_authors');

        $this->dropTable('books_authors');
        echo "Table Books-Authors successful deleted.\n";

        $this->dropTable('authors');
        echo "Table Authors successful deleted.\n";

        $this->dropTable('books');
        echo "Table Books successful deleted.\n";

        $this->dropTable('users');
        echo "Table Users successful deleted.\n";
    }
}

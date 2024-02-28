<?php

namespace app\controllers\api\v1;

use Yii;
use app\models\forms\SubscribesForm;
use app\models\Subscriptions;
use yii\rest\Controller;

class ReportsController extends Controller
{
    /**
     * Отчет топ авторов за год
     * @param int $year
     * @return array|array[]
     * @throws \yii\db\Exception
     */
    public function actionTopAuthors(int $year)
    {
        $sql = <<<SQL
            SELECT a.full_name,
                   max(a.id) max
            FROM authors a
                JOIN books_authors ba ON ba.id_author = a.id
                JOIN books b ON ba.id_book = b.id
            WHERE b.publication_year = $year
            GROUP BY a.id
            ORDER BY max desc
            LIMIT 10       
        SQL;
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return array_map(function ($v) {
            return [
                'fioAuthor' => $v['full_name'],
                'bookCountForYear' => $v['max'],
            ];
        }, $data);
    }
}

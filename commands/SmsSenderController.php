<?php
namespace app\commands;

use app\models\Authors;
use Yii;
use app\models\Subscriptions;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;

class SmsSenderController extends Controller
{
    const DEFAULT_TEST_API_KEY = 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ';
    const DEFAULT_SENDER = 'INFORMATION';

    /**
     * Фоновый шедулер отправки смсок, хромая асинхронщина от пхп
     * @param string $authorList
     * @param string $bookName
     * @return int
     * @throws Exception
     */
    public function actionIndex(string $authorList, string $bookName)
    {
        $authorList = json_decode($authorList, true); // нарочно опускаю всякие проверки
        $limit = 1000;
        $offset = 0;
        $authors = [];
        do {
            $phoneListToSend = Subscriptions::find()
                ->where(['in', 'id_author', $authorList])
                ->limit($limit)
                ->offset($offset)
                ->all();
            if (empty($phoneListToSend)) {
                break;
            }
            $offset += $limit;
            foreach ($phoneListToSend as $subscribe) {
                if (empty($authors[$subscribe->id_author])) {
                    $authors[$subscribe->id_author] = Authors::findOne($subscribe->id_author);
                }
                $this->send(
                    $subscribe->phone,
                    "У автора " . ($authors[$subscribe->id_author]->full_name ?? 'Unknown') . " вышла новая книга: \"$bookName\""
                );
            }
        } while (true);
        return ExitCode::OK;
    }

    /**
     * Сама отправка смски
     * @param string $phone
     * @param string $message
     * @return bool
     */
    protected function send(string $phone, string $message)
    {
        $data = http_build_query([
            'send' => $message,
            'to' => $phone,
            'from' => Yii::$app->params['smsSender']['senderName'] ?? self::DEFAULT_SENDER,
            'apikey' => Yii::$app->params['smsSender']['apiKey'] ?? self::DEFAULT_TEST_API_KEY,
            'format' => 'json',
        ]);
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, "https://smspilot.ru/api.php?$data");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);
        }

        // Ну тут конечно от требований зависит, можно при неудаче и залогировать и положить в очередь на попозже.
        // Опускаю эти нюансы
        return empty($response['error']);
    }
}

<?php
include_once 'init/wa_db_init.php';
$file = "client_wh/".$_POST['subdomain']."_wh.php";
$text = '<?php' ."\n". '$data_json = file_get_contents(\'php://input\');' . "\n" .
        '$subdomain = \'' . $_POST['subdomain'] . '\';' . "\n" .
        '$admin_email = \'' . $_POST['admin_email'] . '\';' . "\n" .
        '$admin_api = \''. $_POST['admin_api'] .'\';' . "\n" .
        '$post_url = \'https://example.com/dashboard/whatsapp_widget/wa_webhooks.php\';' . "\n\n" .
        '////////////////////////////////////' . "\n" .
        '$post_array = array(' . "\n" .
            '\'subdomain\' => $subdomain,' . "\n" .
            '\'admin_email\' => $admin_email,' . "\n" .
            '\'admin_api\' => $admin_api,' . "\n" .
            '\'data_json\' => $data_json' . "\n" .
            ');' . "\n\n" .
            '$post_query = http_build_query($post_array);' . "\n" .
            '$options = array(\'http\' => array(' . "\n" .
                '\'method\' => \'POST\',' . "\n" .
                '\'header\' => \'Content-type: application/x-www-form-urlencoded\',' . "\n" .
                '\'content\' => $post_query' . "\n" .
            ')' . "\n" .
            ');' . "\n\n" .
            '$context = stream_context_create($options);' . "\n" .
            '$result = file_get_contents($post_url, false, $context);' . "\n" .
            '?>';
//////

//если файла нету... тогда
if (!file_exists($file)) {
    $fp = fopen($file, "w"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту),мы создаем файл
    fwrite($fp, $text);
    fclose($fp);
}

////////////////////////////////////////////////////////////////////////////////////////////////////
$wh_url = __DIR__ . '/' . $file;
$wh_url = str_replace('/home/z/zhuravlev/', '', $wh_url);
$wh_url = str_replace('/public_html', '', $wh_url);
$wh_url = 'https://' . $wh_url; // финальный путь к файлу

$data = [
    'set' => true,
    'webhookUrl' => $wh_url
];
$json = json_encode($data); // Закодируем данные в JSON
// URL для запроса POST /message
$url = $_POST['wa_api'] . 'webhook?token=' . $_POST['wa_token'];
// Сформируем контекст обычного POST-запроса
$options = stream_context_create(['http' => [
    'method'  => 'POST',
    'header'  => 'Content-type: application/json',
    'content' => $json
]
]);
// Отправим запрос
$result = file_get_contents($url, false, $options);
//if($result) {
//    $query = "UPDATE wa_requests SET webhooks=? WHERE subdomain=?";
//    $condition = [1,$_POST['subdomain']];
//    $Database_req->updateRow($query,$condition);
//}

print_r($result);
echo "wh url: " . $wh_url;
?>
<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
$wh_url = __DIR__ . '/webhooks.php';
$wh_url = str_replace('/home/z/zhuravlev/', '', $wh_url);
$wh_url = str_replace('/public_html', '', $wh_url);
$wh_url = 'https://' . $wh_url; // финальный путь к файлу
echo "wh_url: " . $wh_url;
$data = [
    'set' => false,
    'webhookUrl' => $wh_url
];
$json = json_encode($data); // Закодируем данные в JSON
// URL для запроса POST /message
$url = 'https://eu10.chat-api.com/instance7922/' . 'webhook?token=' . 'cqecl4pe15e7rv9v'; // поменять здесь на созданные
// Сформируем контекст обычного POST-запроса
$options = stream_context_create(['http' => [
    'method'  => 'POST',
    'header'  => 'Content-type: application/json',
    'content' => $json
]
]);
// Отправим запрос
$result = file_get_contents($url, false, $options);
print_r($result);
?>
<?php
$data = [
    'phone' => '79312760481', // Телефон получателя
    'body' => 'а ну-ка еще один тест', // Сообщение
];
$json = json_encode($data); // Закодируем данные в JSON
// URL для запроса POST /message
$url = 'https://eu6.chat-api.com/instance12344/message?token=6whs7tfjwzezcyk5';
// Сформируем контекст обычного POST-запроса
$options = stream_context_create(['http' => [
    'method'  => 'POST',
    'header'  => 'Content-type: application/json',
    'content' => $json
]
]);
// Отправим запрос
$result = file_get_contents($url, false, $options);

echo "<pre>123";
var_dump($result);
echo "321</pre>";
?>
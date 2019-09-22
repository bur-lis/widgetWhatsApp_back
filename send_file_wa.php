<?php
header('Access-Control-Allow-Origin: *');
include_once 'init/wa_db_init.php';
$subdomain = $_GET['subdomain'];
$receivers = json_decode($_GET['res']);
$query = "SELECT API_key,token FROM wa_requests WHERE subdomain=? LIMIT 1";
$condition = [$subdomain];
$url_table = $Database_req->getRows($query,$condition);

$apiURL = $url_table[0]['API_key'];
$token = $url_table[0]['token'];

$error = false;
$files = array();
$status = [];

$uploaddir = './wa_files/'; // . - текущая папка где находится uploadTemplate.php
// Создадим папку если её нет
if (!is_dir($uploaddir)) mkdir($uploaddir, 0777);
// переместим файлы из временной директории в указанную
$i=0;
$a=0;
if(!empty($_FILES)) {
    foreach ($receivers as $receiver) {
        foreach ($_FILES as $file) {
            if($a==0) {
                $upload = move_uploaded_file($file['tmp_name'], $uploaddir . $file['name']);
            }
            fwrite(fopen("file_logs.txt", "a"), "\n".date('d.m.Y	(H:i:s)')." -  data: ".print_r($uploaddir . $file['name'],true) . "\n-------\n");

            if($upload) {
                $real_path = realpath($uploaddir . $file['name']);
                $file_str = str_replace('/home/z/zhuravlev/', '', $real_path);
                $file_str = str_replace('/public_html', '', $file_str);
                $files[$i] = 'https://' . $file_str; // финальный путь к файлу
                ///////////////////////////////////////
                $data = [
                    'phone' => $receiver, // Телефон получателя
                    'body' => $files[$i], // Сообщение
                    'filename' => $file['name']
                ];
                fwrite(fopen("file_logs.txt", "a"), "\n".date('d.m.Y	(H:i:s)')." -  data: ".print_r($data,true) . "\n-------\n");

                $json = json_encode($data); // Закодируем данные в JSON
                // URL для запроса POST /message
                //            $url = '//eu1.whatsapp.chat-api.com/instance773/message?token=x4zlmnvx5zxin7l4';
                $url = $apiURL . 'sendFile?token=' . $token;
                // Сформируем контекст обычного POST-запроса
                $options = stream_context_create(['http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/json',
                    'content' => $json
                ]
                ]);

                // Отправим запрос
                $result = json_decode(file_get_contents($url, false, $options));


                $status[] = $result->sent;
                fwrite(fopen("file_logs.txt", "a"), "\n".date('d.m.Y	(H:i:s)')." -  data: ".print_r($result->sent,true) . "\n-------\n");

                ///////////////////////////////////////
                $i++;
            }
        }
    $a++;
    }
    echo json_encode($status);
}
else {
    echo json_encode([1]);;
}
?>
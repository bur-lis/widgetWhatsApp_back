<?php
    include_once 'init/wa_db_init.php';

    $queryUpdate = "UPDATE wa_requests SET date_access = null, state = 'delete', 
                     API_key = null, token = null, google_account = null, client1 = '', client2 = '', client3 = ''
              WHERE date_access = CURRENT_DATE()";
    $querySelect = "SELECT google_account FROM wa_requests
                    WHERE date_access = CURRENT_DATE()";
    $table_ChatApiId = $Database_req->getRows($querySelect);
    
    foreach ($table_ChatApiId as $key => $value) {
        // создаем подключение
        $ch = curl_init('https://us-central1-app-chat-api-com.cloudfunctions.net/deleteInstance');
        // устанавлваем даные для отправки
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'uid=bIGSksk17XTbg5MwMpaNoq47t7C2&instanceId='.$value['google_account']);
        // формат отправляемых данных
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        // флаг о том, что нужно получить результат
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // отправляем запрос
        $response = curl_exec($ch);
        // закрываем соединение
        curl_close($ch);
    }

    $Database_req->updateRow($queryUpdate);
?>
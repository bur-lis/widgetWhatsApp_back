<?php
include_once 'init/wa_db_init.php';

if ($secret_key !== 123454321) {
    throw new Exception("Not equal secret key");
}

// $subdomain = $_REQUEST["label"];
echo $subdomain;

    // создаем подключение
	$ch = curl_init('https://us-central1-app-chat-api-com.cloudfunctions.net/newInstance');
	// устанавлваем даные для отправки
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'uid=jBS8q01wGKYdH2uGZWbILoHviIH3&type=whatsapp');
    // формат отправляемых данных
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
	// флаг о том, что нужно получить результат
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// отправляем запрос
	$response = curl_exec($ch);
	// закрываем соединение
    curl_close($ch);

    $fd = fopen("response.txt", 'w') or die("не удалось создать файл");
    fwrite($fd, $response);
    fclose($fd);
    $response = json_decode($response);

    $apiUrl = $response->result->instance->apiUrl;
    $token = $response->result->instance->token;
    $id = $response->result->instance->id;
    
    $query = "UPDATE wa_requests SET API_key=?, token=?, google_account=? WHERE subdomain=?";
    $condition = [$apiUrl, $token, $id, $subdomain];
    $Database_req->updateRow($query, $condition);
    
    // echo $response->result->status;
    // echo var_dump($response);e

    $url = array(
        "webhookUrl" => "https://prosto.group/dashboard/whatsapp_widget_lis/getWebhookFromChatApi.php?user=" . $subdomain
    );
    // создаем подключение
	$ch = curl_init($apiUrl . "webhook?token=" . $token);
	// устанавлваем даные для отправки
	curl_setopt($ch, CURLOPT_POSTFIELDS, $url);
	// флаг о том, что нужно получить результат
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	// отправляем запрос
	$response = curl_exec($ch);
	// закрываем соединение
    curl_close($ch);

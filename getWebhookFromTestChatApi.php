<?php

ini_set("display_errors", 'on');
error_reporting(E_ALL);

//file_put_contents('logs/log.json', "eeee");

include_once 'init/wa_db_init.php';
$data = file_get_contents('php://input');

$messages = json_decode($data)->{'messages'};

file_put_contents('logs/log15.json', print_r(json_decode($data), 1), FILE_APPEND);

foreach ($messages as $message) {
    $chat_id = $message -> {"chatId"};
    if (count(explode("-", $chat_id)) > 1) {
        continue;
    }
    $phone = explode("@", $chat_id)[0];


    $query = "SELECT * FROM `wa_requests` WHERE client1 = ? OR  client2 = ? OR  client3 = ?";
    $condition = [$phone, $phone, $phone];
    $table_id = $Database_req->getRows($query, $condition);


    if (count($table_id) != 1) {
        continue;
    }
    $row = $table_id[0];

    $subdomain = $row['subdomain'];

    $admin_api = $row['AMO_API_key'];
    $admin_email = $row['AMO_admin_email'];
    file_put_contents('logs/log11.json', $subdomain . ' - ' . $admin_email . ' - ' . $admin_api);
    
    $post_url = 'https://prosto.group/dashboard/whatsapp_widget_lis/wa_webhooks.php';

    $post_array = array(
        'subdomain' => $subdomain,
        'admin_email' => $admin_email,
        'admin_api' => $admin_api,
        'data_json' => $data
    );



    $post_query = http_build_query($post_array);
    $options = array('http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $post_query
        )
    );

    $context = stream_context_create($options);
    file_get_contents($post_url, false, $context);
}

<?php
    include_once 'init/wa_db_init.php';
    header('Content-type: application/json');

    $client_name = trim($_POST['client_name']);
    $client_number = $_POST['client_number'];
    $client_subdomain = $_POST['client_subdomain'];
    $admin_api = $_POST['apikey_subdomen'];
    $admin_email = $_POST['admin_email'];

    file_put_contents('logs/log.json', $client_number . ' - ' . $client_name . ' - ' . $admin_api . ' - ' . $client_subdomain, FILE_APPEND);

    // если все обязательные поля введены
    if ($client_number && $client_name && $admin_api && $client_subdomain) {

        $curr_date = date("Y-m-d H:i:s");
        $query = "INSERT INTO wa_requests (client_name, client_number, subdomain, date_request, state, AMO_admin_email, AMO_API_key) VALUES(?,?,?,?,?,?,?)";
        $condition = [$client_name, $client_number, $client_subdomain, $curr_date, 'start', $admin_email, $admin_api];
        $Database_req->insertRow($query, $condition);

        $query = "DROP TABLE IF EXISTS wa_blocks_" . $client_subdomain;
        $Database_wa->updateRow($query);
        
        // создаем для него уникальную табличку
        $query = "CREATE TABLE wa_blocks_" . $client_subdomain . " LIKE wa_blocks";
        $Database_wa->updateRow($query);

        $query = "INSERT INTO wa_blocks_" . $client_subdomain . " SELECT * FROM wa_blocks;";
        $Database_wa->insertRow($query);

        // находим id заявки
        $query = "SELECT request_id FROM wa_requests ORDER BY request_id DESC LIMIT 1";
        $table_id = $Database_req->getRows($query);
        $req_id = $table_id[0]['request_id'];
        // if ($req_id) { // если заявка добавилась
        //     //kondrashovanna@prosto.group
        //     $mailto1 = "kondrashovanna@prosto.group"; // кому скидываются заявки
        //     $mailto2 = "zhuravlev@prosto.group"; // кому скидываются заявки
        //     $subject = "Заявка на What'App #" . $req_id;
        //     $message = "Подана заявка" . "\n" .
        //         "Имя клиента: " . $client_name . "\n" .
        //         "Телефон: " . $client_number . "\n" .
        //         "Субдомен: " . $client_subdomain;
        //     $header = "From: email@prosto.group \r\n";

        //     mail($mailto1, $subject, $message, $header);
        //     mail($mailto2, $subject, $message, $header);
        // }
    } else { // если номер некорректный
        $req_id = 'Bug';
    }


?>
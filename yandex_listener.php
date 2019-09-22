<?php
include_once 'init/wa_db_init.php';
header('Content-Type: text/html; charset=utf-8');

file_put_contents(__DIR__ . '/logs/pays.txt', print_r($_POST, true) . PHP_EOL, FILE_APPEND | LOCK_EX);

$secret = 'pQ1qqS+wYuwenkNApkToJVfS';
$str = $_POST['notification_type'] . '&' .
    $_POST['operation_id'] . '&' .
    $_POST['amount'] . '&' .
    $_POST['currency'] . '&' .
    $_POST['datetime'] . '&' .
    $_POST['sender'] . '&' .
    $_POST['codepro'] . '&' .
    $secret . '&' .
    $_POST['label'];

if ($_POST['sha1_hash'] == sha1($str)) { //если деньги пришли

    file_put_contents(__DIR__ . '/logs/1.txt', sha1($str) . PHP_EOL, FILE_APPEND | LOCK_EX);

    $subdomain = substr($_POST['label'], strlen('widgetWA'));

    file_put_contents(__DIR__ . '/logs/1_2.txt', $subdomain . " " . $_POST['label'], FILE_APPEND | LOCK_EX);


    $pay_arr = [2500, 7200, 13800]; // массив с ценами на виджет
    $time_arr = [1, 3, 6]; // массив с месяцами

    for ($i = 0; $i < count($pay_arr); $i++) {
        if ($pay_arr[$i] == $_POST['withdraw_amount']) { // если пришло i-тое количество денег

            file_put_contents(__DIR__ . '/logs/2.txt', $pay_arr[$i] . PHP_EOL, FILE_APPEND | LOCK_EX);

            $query = "SELECT request_id, client_name, client_number, subdomain, date_access FROM wa_requests WHERE subdomain=? LIMIT 1";
            $condition = [$subdomain];
            $user_info_table = $Database_req->getRows($query, $condition);

            $req_id = $user_info_table[0]['request_id'];
            $client_name = $user_info_table[0]['client_name'];
            $client_number = $user_info_table[0]['client_number'];
            $client_subdomain = $user_info_table[0]['subdomain'];

            $date_access = $user_info_table[0]['date_access'];
            $date_now = date('Y-m-d H:i:s');

            if ($date_now > $date_access) { // если просрочен или оплата первая
                file_put_contents(__DIR__ . '/logs/3.txt', $date_now . PHP_EOL, FILE_APPEND | LOCK_EX);

                if (is_null($date_access)) { // если виджет впервые покупается
                    file_put_contents(__DIR__ . '/logs/4.txt', $date_access . PHP_EOL, FILE_APPEND | LOCK_EX);
                    // устанавливаем новый срок и статус payed
                    $fin_date = date('Y-m-d H:i:s', strtotime($date_now . "+" . $time_arr[$i] . " month"));
                    $query = "UPDATE wa_requests SET date_access=?,state=? WHERE subdomain=?";
                    $condition = [$fin_date, 'payed', $subdomain];
                    $Database_req->updateRow($query, $condition);

                    // создаем для него уникальную табличку
                    $query = "CREATE TABLE wa_blocks_" . $subdomain . " LIKE wa_blocks";
                    $Database_wa->updateRow($query);
                    $query = "INSERT INTO wa_blocks_" . $subdomain . " SELECT * FROM wa_blocks;";
                    $Database_wa->insertRow($query);

                    // отправляем почту
                    $mailto1 = "example2@example.com"; // кому скидываются заявки
                    $mailto2 = "example1@example.com"; // кому скидываются заявки
                    $subject = "Заявка на What'App #" . $req_id;
                    $message =
                        "Заявка ОПЛАЧЕНА" . "\n" .
                        "Имя клиента: " . $client_name . "\n" .
                        "Телефон: " . $client_number . "\n" .
                        "Субдомен: " . $client_subdomain;
                    $header = "From: email@example.com \r\n";

                    mail($mailto1, $subject, $message, $header);
                    mail($mailto2, $subject, $message, $header);


                } else { // если виджет просрочен
                    file_put_contents(__DIR__ . '/logs/5.txt', "Если виджет просрочен" . PHP_EOL, FILE_APPEND | LOCK_EX);

                    // устанавливаем новый срок
                    $fin_date = date('Y-m-d H:i:s', strtotime($date_now . "+" . $time_arr[$i] . " month"));
                    $query = "UPDATE wa_requests SET date_access=? WHERE subdomain=?";
                    $condition = [$fin_date, $subdomain];
                    $Database_req->updateRow($query, $condition);
                }
            } else {                      // если виджет используется, но хотят еще продлить
                file_put_contents(__DIR__ . '/logs/6.txt', "если виджет используется, но хотят еще продлить" . PHP_EOL, FILE_APPEND | LOCK_EX);

                $fin_date = date('Y-m-d H:i:s', strtotime($date_access . "+" . $time_arr[$i] . " month"));
                $query = "UPDATE wa_requests SET date_access=? WHERE subdomain=?";
                $condition = [$fin_date, $subdomain];
                file_put_contents(__DIR__ . '/logs/6_cond.txt', $fin_date . " " . $subdomain . PHP_EOL, FILE_APPEND | LOCK_EX);
                $dbRes = $Database_req->updateRow($query, $condition);
                file_put_contents(__DIR__ . '/logs/6_db.txt', print_r($dbRes, true) . PHP_EOL, FILE_APPEND | LOCK_EX);


                // отправляем почту
                $mailto1 = "rivza.cat@example.com";
                $mailto2 = "zhuravlev@example.com"; // кому скидываются заявки
                $mailto3 = "timger98@gmail.com"; // кому скидываются заявки
                $subject = "Заявка на What'App #" . $req_id;
                $message =
                    "Заявка ОПЛАЧЕНА" . "\n" .
                    "Имя клиента: " . $client_name . "\n" .
                    "Телефон: " . $client_number . "\n" .
                    "Субдомен: " . $client_subdomain;
                $header = "From: email@example.com \r\n";

                $res = mail($mailto1, $subject, $message, $header);
                mail($mailto2, $subject, $message, $header);
                mail($mailto3, $subject, $message, $header);

                file_put_contents(__DIR__ . '/logs/6_email.txt', $res . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }
    }
} else {
    file_put_contents(__DIR__ . '/logs/else.txt', print_r($_POST, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
}


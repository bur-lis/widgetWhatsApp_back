<?php

    include_once 'addNewUser.php';

    $testApiKey = "https://eu15.chat-api.com/instance48696/";
    $testToken = "ca9jsfeiiowsh498";
    $testGoogleAccount = 48696;

    $array_client_numbers = array($_POST['client_number1'], $_POST['client_number2'], $_POST['client_number3']);
    
    $query = "SELECT * FROM `wa_requests` WHERE (client1 = ? AND client1 != '') OR  (client2 = ? AND client2 != '') OR  (client3 = ? AND client3 != '')";
    
    function bool_array($item) {
        global $Database_req, $query;
        $condition = [$item, $item, $item];
        $table = $Database_req->getRows($query, $condition);

        return !count($table);
    }
    function once_true($carry, $item) {return $carry || $item;}
    function all_true($carry, $item) {return $carry && $item;}

    $querySucsses = "SELECT *  FROM wa_requests WHERE subdomain = ?";
    $condition = [$client_subdomain];
    $table_sucsses = $Database_req->getRows($querySucsses,$condition);

    $message = 'Что то пошло не так :(';
    $succes = false;

    if (array_reduce($array_client_numbers, 'once_true', false)) {

        $count_tables = array_map('bool_array',$array_client_numbers);
        $all_true = array_reduce($count_tables, 'all_true', true);

        
        if ($all_true && count($table_sucsses)) {
            $queryUpdate = "UPDATE wa_requests SET date_access = ADDDATE(CURRENT_DATE(), INTERVAL 1 DAY), state = 'test', 
                            API_key = ?, token = ?, google_account = ?,
                            client1 = ?, client2 = ?, client3 = ?
                            WHERE subdomain = ?";

            $condition = [$testApiKey, $testToken, $testGoogleAccount, $array_client_numbers[0],
                          $array_client_numbers[1],$array_client_numbers[2],$client_subdomain];
            $Database_req->updateRow($queryUpdate,$condition);

            $message = 'Успешно';
            $success = true;
        } else {
            $numbers_message = 'Номер(а): ';
            foreach ($count_tables as $key => $value) {
                if (!$value) $numbers_message .= $array_client_numbers[$key] . '<br> ';
            }
            $message = $numbers_message . 'уже ипользуются другими тестовыми пользователями';
        }
    } else {
        if (count($table_sucsses) > 0) $message = 'Вы не ввели ни одного номера';
        else  $message = 'Ошибка! Возможно Вы ввели не все данные';
    }
    
    $querySelect = "SELECT subdomain,date_access,state FROM wa_requests WHERE subdomain = ?";
    $condition = [$client_subdomain];
    $table_id = $Database_req->getRow($querySelect, $condition);

    $curr_sub = $table_id['subdomain'];
    $fin_date = $table_id['date_access'];
    $state = $table_id['state'];

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => [
            'date' => $fin_date, 
            'state' => $state, 
            'subdomain' => $curr_sub
        ]
    ]);
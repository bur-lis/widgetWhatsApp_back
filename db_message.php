<?php
include_once 'init/wa_db_init.php';

file_put_contents('logs.txt', "GET " . print_r($_GET, true) . "\nPOST " . print_r($_POST, true) . PHP_EOL, FILE_APPEND | LOCK_EX);

const TEST_ACCESS_INSTANCE = "https://eu15.chat-api.com/instance48696/";

function getCountMessagesBySubdomain($subdomain)
{
    global $Database_wa;
    $query = "SELECT count(*) as count FROM messages_history WHERE subdomain = ?";
    $vals = [$subdomain];
    $res = $Database_wa->getRows($query, $vals);
    $count = $res[0]['count'];
    return $count;
}

function setMessageToHistory($subdomain, $apiUrl, $phone, $text, $result)
{
    global $Database_wa;
    $query = "INSERT INTO messages_history (id,time,subdomain,api_url,phone,text,result) VALUES ('',?,?,?,?,?,?)";
    $vals = [time(), $subdomain, $apiUrl, $phone, $text, $result];
    $Database_wa->insertRow($query, $vals);
}

function sendByAllContacts($subdomain, $phones, $message, $apiURL, $token)
{
    $resArr = [];
    for ($i = 0; $i < count($phones); $i++) { // отправка сообщения всем контантам
        $data = [
            'phone' => $phones[$i], // Телефон получателя
            'body' => $message, // Сообщение
        ];
        $json = json_encode($data);
        $url = $apiURL . 'message?token=' . $token;

        $http_params = ['http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => $json
        ]];

        $options = stream_context_create($http_params);
        file_put_contents(__DIR__."/logs/govnichebleat_kolenb.txt",print_r($http_params, true));
        // file_put_contents(__DIR__."/logs/govnichebleat_kolenb2.txt",print_r($url, true));

        $post = file_get_contents($url, false, $options);
        $resArr[$i] = $post['sent'];

        ob_start();
        var_dump($post['sent']);
        $output = ob_get_clean();

        file_put_contents(__DIR__."/logs/logs.txt", $apiURL);
        file_put_contents(__DIR__."/logs/logs.txt", $url, FILE_APPEND);

        setMessageToHistory($subdomain, $apiURL, $phones[$i], $message, json_encode($post));
    }
    
    return ['success' => true, 'result' => $resArr];
}

if (isset($_POST['tool'])) { // если в скрипт пришли для каких-то модификаций
    $tool = $_POST['tool'];
    // если редактирование блока ///////////////////////////////////////////////////////////////////////////////////////
    if ($tool === 'edit') {
        $edit_id = $_POST['id'];
        $edit_name = $_POST['name'];
        $edit_text = $_POST['text'];
        $subdomain = $_POST['subdomain'];

        $query = "UPDATE wa_blocks_" . $subdomain . " SET title=?, text=? WHERE idBlock=?";
        $condition = [$edit_name, $edit_text, $edit_id];
        $Database_wa->updateRow($query, $condition);
        echo "Edit is successful";
    }

    if ($tool === 'infosetting') {
        $subdomain = $_POST['subdomain'];
        
        $query = "SELECT * FROM wa_requests WHERE subdomain=? LIMIT 1";
        $condition = [$subdomain];
        $table = $Database_req->getRows($query, $condition);
        $AMO_API_key = $table[0]['AMO_API_key'];
        echo json_encode($AMO_API_key);
        //echo "lll";
    }

    // если добавление блока ///////////////////////////////////////////////////////////////////////////////////////////
    if ($tool === 'add') {
        $add_name = $_POST['name'];
        $add_text = $_POST['text'];
        $subdomain = $_POST['subdomain'];

        // проверяется, добавляется блок какого-то типа (list или text) или позиция (без типа)
        if (isset($_POST['block_type'])) { // если добавление блока
            $block_type = $_POST['block_type'];

            $query = "SELECT MAX(queue) FROM wa_blocks_" . $subdomain; // находим максимальное значение в очереди
            $queue_table = ($Database_wa->getRows($query));

            $queue = $queue_table[0]['MAX(queue)'] - 1; //        6находим, на какое место встанет новый блок
            $pre_last_queue = $queue_table[0]['MAX(queue)']; // 7 находим, на каком место встанет конец сообщение
            $last_queue = $queue_table[0]['MAX(queue)'] + 1; //   8 находим, на каком место встанет ps

            $query = "UPDATE wa_blocks_" . $subdomain . " SET queue=? WHERE queue=?";
            $condition1 = [$last_queue, $pre_last_queue];
            $condition2 = [$pre_last_queue, $queue];

            // перемещаем блоки в очереди
            $Database_wa->updateRow($query, $condition1); // предпоследний элемент делаем последний
            $Database_wa->updateRow($query, $condition2); // последний элемент делаем следующим

            // вставляем новый блок
            $query = "INSERT INTO wa_blocks_" . $subdomain . " VALUES(?,?,?,?,?,?)";
            $condition = [-1, $add_name, $add_text, $block_type, $queue, NULL];
            $Database_wa->insertRow($query, $condition);

            // находим id нового блока
            $query = "SELECT idBlock FROM wa_blocks_" . $subdomain . " WHERE queue=? LIMIT 1";
            $condition = [$queue];
            $table_id = $Database_wa->getRows($query, $condition);

            $block_id = '';
            $block_id = $table_id[0]['idBlock'];

            echo json_encode($block_id);
        }

        if (isset($_POST['parent'])) { // если добавление позиции
            $own = $_POST['parent'];
            $query = "INSERT INTO wa_blocks_" . $subdomain . " VALUES(?,?,?,?,?,?)";
            $condition = [-1, $add_name, $add_text, "position", NULL, $own];
            $Database_wa->insertRow($query, $condition);

            // узнаем id позиции
            $query = "SELECT idBlock FROM wa_blocks_" . $subdomain . " ORDER BY idBlock DESC LIMIT 1";
            $table_id = $Database_wa->getRows($query);

            $position_id = '';
            $position_id = $table_id[0]['idBlock'];

            echo json_encode($position_id);
        }
    }

    // если удаление блока /////////////////////////////////////////////////////////////////////////////////////////////
    if ($tool === 'delete') {
        $id = $_POST['id'];
        $subdomain = $_POST['subdomain'];

        $query = "SELECT queue FROM wa_blocks_" . $subdomain . " WHERE idBlock=? LIMIT 1"; // ищем номер в очереди удаляемого блока
        $condition = [$id];

        $table_queue = $Database_wa->getRows($query, $condition);
        $queue = '';
        $queue = $table_queue[0]['queue'];

        $query = "UPDATE wa_blocks_" . $subdomain . " SET queue = queue-1 WHERE queue > ?"; // сдвигаем все блоки наверх в очереди
        $condition = [$queue];
        $Database_wa->updateRow($query, $condition);

        $query = "DELETE FROM wa_blocks_" . $subdomain . " WHERE idBlock=?"; // удаляем блок
        $condition = [$id];
        $Database_wa->deleteRow($query, $condition);

        $query = "DELETE FROM wa_blocks_" . $subdomain . " WHERE own=?"; // удаляем его позиции, если это окажется списковый1 блок
        $Database_wa->deleteRow($query, $condition);

        echo "delete successful";
    }

    // если именение очереди блоков ////////////////////////////////////////////////////////////////////////////////////
    if ($tool === 'queue') {
        $id = $_POST['id'];
        $direction = $_POST['direction'];
        $subdomain = $_POST['subdomain'];

        $query = "SELECT queue FROM wa_blocks_" . $subdomain . " WHERE idBlock=? LIMIT 1"; // ищем номер в очереди передвигаемого блока
        $condition = [$id];

        $table_queue = $Database_wa->getRows($query, $condition);
        $queue = '';
        $queue = $table_queue[0]['queue'];
        
        $query = "UPDATE wa_blocks_" . $subdomain . " SET queue=? WHERE queue=?";

        // В зависимости от направления двигаем блоки в очереди (задаем условия для прередвижения)
        if ($direction === 'up') {
            $condition1 = [0, $queue - 1];
            $condition2 = [$queue - 1, $queue];
            $condition3 = [$queue, 0];
        } else {
            $condition1 = [0, $queue + 1];
            $condition2 = [$queue + 1, $queue];
            $condition3 = [$queue, 0];
        }

        $Database_wa->updateRow($query, $condition1);
        $Database_wa->updateRow($query, $condition2);
        $Database_wa->updateRow($query, $condition3);
    }

    // если отправка сообщений /////////////////////////////////////////////////////////////////////////////////////
    if ($tool === 'sending') {
        $phones = $_POST['phones'];
        $message = $_POST['message'];
        $subdomain = $_POST['subdomain'];
        $status = [];

        $query = "SELECT API_key,token FROM wa_requests WHERE subdomain=? LIMIT 1";
        $condition = [$subdomain];
        $url_table = $Database_req->getRows($query, $condition);
        $apiURL = $url_table[0]['API_key'];
        $token = $url_table[0]['token'];
        $myarray = [
            'API' => $apiURL,
            'token' => $token
        ];

        if ($apiURL == TEST_ACCESS_INSTANCE) {
file_put_contents('logs/log1.json', "faфффil");
            
            function sum($carry, $phone)
            {
                global $Database_req, $subdomain;
                $query = "SELECT * FROM `wa_requests` WHERE subdomain = '$subdomain' AND (client1 = ? OR  client2 = ? OR  client3 = ?)";
                $condition = [$phone, $phone, $phone];
                $table_id = $Database_req->getRows($query, $condition);
                return $carry && count($table_id) == 1;
            }

            $countMessages = getCountMessagesBySubdomain($subdomain);
            
           // if ($countMessages > 20) {
                $result=['success' => false, 'message'=>'Вы превысили лимит сообщений'];                
           // } else
             if(!array_reduce($phones, 'sum', true)) {
                $result=['success'=> false, 'message'=>'Номер телефона не совпадает с указанными'];
            } else{
                $result = sendByAllContacts($subdomain, $phones, $message, $apiURL, $token);
            }
        } else {
                $result = sendByAllContacts($subdomain, $phones, $message, $apiURL, $token);
        }
        echo json_encode($result);
    }

    // если проверяется на наличие клиент //////////////////////////////////////////////////////////////////////////////
    if ($tool === 'client') {
        $user_sub = $_POST['user_subdomain'];

        $query = "SELECT subdomain,date_access,state FROM wa_requests WHERE subdomain=? LIMIT 1";
        $condition = [$user_sub];
        $table_id = $Database_req->getRows($query, $condition);

        $curr_sub = false; // возвращает subdomain или false: есть пользователь в таблице или нет
        $fin_date = false;
        $state = false;

        if (!empty($table_id)) {
            $curr_sub = $table_id[0]['subdomain'];
            $fin_date = $table_id[0]['date_access'];
            $state = $table_id[0]['state'];
        }

        $user_info = ['subdomain' => $curr_sub,
            'date' => $fin_date,
            'state' => $state];

        echo json_encode($user_info);
    }

    // если отправляется заявка на виджет //////////////////////////////////////////////////////////////////////////////
    if ($tool === 'request') {
        $client_name = trim($_POST['client_name']);
        $client_number = $_POST['client_number'];
        $client_subdomain = $_POST['client_subdomain'];
        $admin_email = $_POST['admin_email'];

        if ($client_number && $client_name) { // если номер корректный

            //kondrashovanna@prosto.group
            $mailto1 = "byrakova.lis@gmail.com"; // кому скидываются заявки
            // $mailto2 = "zhuravlev@prosto.group"; // кому скидываются заявки
            $subject = "Заявка на What'App";
            $message = "Подана заявка" . "\n" .
                "Имя клиента: " . $client_name . "\n" .
                "Телефон: " . $client_number . "\n" .
                "Субдомен: " . $client_subdomain;
            $header = "From: email@prosto.group \r\n";

            mail($mailto1, $subject, $message, $header);
            mail($mailto2, $subject, $message, $header);


            echo json_encode(['success' => true]);
        } else { // если номер некорректный
            echo json_encode(['success' => false]);
        }
    }

    // если запрос на доступ пользователя к виджету //////////////////////////////////////////////////////////////////////////////
    if ($tool === 'access') {
        $client_subdomain = $_POST['subdomain'];

        $query = "SELECT date_access, state FROM wa_requests WHERE subdomain=? LIMIT 1";
        $condition = [$client_subdomain];
        $table_id = $Database_req->getRows($query, $condition);

        $fin_date = false;
        $state = false;

        $fin_date = $table_id[0]['date_access'];
        $state = $table_id[0]['state'];

        $data = ['date' => $fin_date,
            'state' => $state];

        echo json_encode($data);
    }

    // если запрос на изменения в окне настроек //////////////////////////////////////////////////////////////////////////////
    if ($tool === 'settings_save') {
        $client_subdomain = $_POST['subdomain'];
        $admin_email = $_POST['admin_email'];
        $admin_api = $_POST['admin_api'];

        $query = "UPDATE wa_requests SET AMO_admin_email=?,AMO_API_key=? WHERE subdomain=?";
        $condition = [$admin_email, $admin_api, $client_subdomain];
        $Database_req->updateRow($query, $condition);
    }
} else { // если просто запрос на данные таблицы
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Информация о блоках сообщения                                                                                      //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $subdomain = $_POST['subdomain'];
    $query = "SELECT idBlock, title, text FROM wa_blocks_" . $subdomain . " WHERE type != 'position' ORDER BY queue ASC";
    $table_blocks = $Database_wa->getRows($query);  // таблица с блоками сообщения
//создаем массив вида:
// [idBlock => [Название, Текст],
//  idBlock => [Название, Текст],
//   ...                        ]
    $block_info_array = [];
    foreach ($table_blocks as $string) {
        $id = $string['idBlock'];

        $block_content_array = [$string['title'], $string['text']];
        $block_info_array += ['+' . $id => $block_content_array];
    }
    
//    echo "<b>Блоки: </b><br>";
//    echo "<pre>";
//    print_r($block_info_array);
//    echo "</pre>";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Информация о списковых блоках                                                                                      //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $query = "SELECT idBlock FROM wa_blocks_" . $subdomain . " WHERE type = 'block_list'"; // вытаскиваем списковые блоки
    $table_idBlocks = $Database_wa->getRows($query); // таблица с id списковых блоков

    $query = "SELECT idBlock, title, text, own FROM wa_blocks_" . $subdomain . " WHERE type = 'position'"; // вытаскиваем позиции этих блоков
    $table_positions = $Database_wa->getRows($query); // таблица чекбоксов с их блоками-хозяинами

// создаем массив вида:
// [idBlock => [idPosition => [Название, Текст],
//              idPosition => [Название, Текст],
//               ...                           ],
// idBlock => [idPosition => [Название, Текст],
//             idPosition => [Название, Текст],
//               ...                           ],
//  ...
// ]

    $positions_array = [];
    foreach ($table_idBlocks as $string) {
        $id = $string['idBlock'];
        $id_array_positions = [];

        foreach ($table_positions as $position) {
            if ($position['own'] == $id) {
                $id_array_position = [$position['idBlock'] => [$position['title'], $position['text']]];
                $id_array_positions += $id_array_position;
            }
        } // ассоциативный массив позиций для конкретного id блока

        $positions_array += [$id => $id_array_positions];
    }
//    echo "<b>Позиции: </b><br>";
//    echo "<pre>";
//    print_r($positions_array);
//    echo "</pre>";

    // отправляем данные обратно на клиент в формате json

    echo json_encode([$block_info_array, $positions_array]);
}

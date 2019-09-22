<?php
include_once '../../auth.php';
require __DIR__ . '/../../view/layouts/menu/messagesMenu.php';
include_once '../../app/db_init.php';

$query = "SELECT * FROM wa_requests WHERE state<>'used'";
$table_req = $Database->getRows($query);

$date_now = date('Y-m-d H:i:s');
$query = "SELECT * FROM wa_requests WHERE (state='used') AND (date_access<'" . $date_now . "')";
$table_overdue = $Database->getRows($query);

$query = "SELECT * FROM wa_requests WHERE state='used' AND (date_access>'" . $date_now . "')";
$table_used = $Database->getRows($query);

echo '<link type="text/css" rel="stylesheet" href="style.css" >';
$tool_img = '<img width="20px;" src="images/tool.png">';
echo '<div style="width:100%; padding: 0px 20px;">';

echo '<h3 style="color: #003066">Пользователи, используюшие виджет</h3>';

echo '<table id="table-requests">';
echo '<tr id="header">';
echo '<td>№ заявки</td>';
echo '<td>Имя</td>';
echo '<td>Телефон</td>';
echo '<td>Субдомен</td>';
echo '<td nowrap>Время заявки</td>';
echo '<td nowrap>Время окончания действия виджета</td>';
echo '<td>Статус</td>';
echo '<td>Линк</td>';
echo '<td>API ключ</td>';
echo '<td>Токен</td>';
echo '<td>Google аккаунт</td>';
echo '<td>Google пароль</td>';
echo '<td>AMO логин админа</td>';
echo '<td>AMO API ключ админа</td>';
//echo        '<td>Коментарий</td>';
echo '<td>Вебхуки <!--(должны быть заполнены субдомен, API ключ, токен, AMO логин админа, AMO API ключ админа)!--></td>';
//echo        '<td>QR-код (должны быть заполнены API ключ, токен)</td>';
//echo        '<td>' . $tool_img . '</td>';
echo '</tr>';

foreach ($table_used as $string) {
    $link = 'https://example.com/whatsapp/settings/?domain=' . $string['subdomain'] . '&api=' . $string['AMO_API_key'];
    echo '<tr>';
    echo '<td>' . $string['request_id'] . '</td>';
    echo '<td>' . $string['client_name'] . '</td>';
    echo '<td>' . $string['client_number'] . '</td>';
    echo '<td>' . $string['subdomain'] . '</td>';
    echo '<td>' . $string['date_request'] . '</td>';
    echo '<td>' . $string['date_access'] . '</td>';
    echo '<td>' . $string['state'] . '</td>';
    ?>
    <td>
        <a href="<?= $link ?>"><?=$link ?></a>
    </td>
    <?php
    echo '<td>' . $string['API_key'] . '</td>';
    echo '<td>' . $string['token'] . '</td>';
    echo '<td>' . $string['google_account'] . '</td>';
    echo '<td>' . $string['google_pasword'] . '</td>';
    echo '<td>' . $string['AMO_admin_email'] . '</td>';
    echo '<td>' . $string['AMO_API_key'] . '</td>';
//    echo    '<td>' . $string['comment'] . '</td>';
    echo '<td>';
    echo ($string['webhooks'] == 0) ? ('<input type="button" class="tool-btn wh-on" data-subdomain="' . $string['subdomain'] . '" data-waapi="' . $string['API_key'] . '" data-watoken="' . $string['token'] . '" data-adminemail="' . $string['AMO_admin_email'] . '" data-adminkey="' . $string['AMO_API_key'] . '" value="ПРИКРЕПИТЬ">') : ('Прикреплены');
    echo '</td>';
//    echo        '<td><input type="button" class="tool-btn qr-send" data-waapi="'.$string['API_key'].'" data-watoken="'.$string['token'].'" value="ОТПРАВИТЬ"><br>E-mail: <input type="text" id="qr-email"></td>';
//    echo    '<td>' .
//        '<img class="edit-tool" id="' . $string['request_id'] . '" width="20px;" style="margin-right: 20px;" src="images/edit.png">' .
//        '<img class="delete-tool" id="' . $string['request_id'] . '" width="20px;" src="images/delete.png">' .
//        '</td>';
//    echo '</tr>';
}

echo '<h3 style="color: #003066">Заявки на виджет</h3>';
echo '<table id="table-requests">';
echo '<tr id="header">';
echo '<td>№ заявки</td>';
echo '<td>Имя</td>';
echo '<td>Телефон</td>';
echo '<td>Субдомен</td>';
echo '<td>Время заявки</td>';
echo '<td>Время окончания действия виджета</td>';
echo '<td>Статус</td>';
echo '<td>API ключ</td>';
echo '<td>Токен</td>';
echo '<td>Google аккаунт</td>';
echo '<td>Google пароль</td>';
echo '<td>AMO логин админа</td>';
echo '<td>AMO API ключ админа</td>';
//    echo        '<td>Комментарий</td>';
echo '<td>Вебхуки (должны быть заполнены субдомен, API ключ, токен, AMO логин админа, AMO API ключ админа)</td>';
//    echo        '<td>QR-код (должны быть заполнены API ключ, токен)</td>';
//    echo        '<td>' . $tool_img . '</td>';
echo '</tr>';

foreach ($table_req as $string) {
    echo ($string['state'] === 'payed') ? '<tr style="background: #ccff99;">' : '<tr>';
    echo '<td>' . $string['request_id'] . '</td>';
    echo '<td>' . $string['client_name'] . '</td>';
    echo '<td>' . $string['client_number'] . '</td>';
    echo '<td>' . $string['subdomain'] . '</td>';
    echo '<td nowrap>' . $string['date_request'] . '</td>';
    echo '<td>' . $string['date_access'] . '</td>';
    echo '<td>' . $string['state'] . '</td>';
    echo '<td>' . $string['API_key'] . '</td>';
    echo '<td>' . $string['token'] . '</td>';
    echo '<td>' . $string['google_account'] . '</td>';
    echo '<td>' . $string['google_pasword'] . '</td>';
    echo '<td>' . $string['AMO_admin_email'] . '</td>';
    echo '<td>' . $string['AMO_API_key'] . '</td>';
//        echo    '<td>' . $string['comment'] . '</td>';
    echo '<td>';
    echo ($string['webhooks'] == 0) ? ('<input type="button" class="tool-btn wh-on" data-subdomain="' . $string['subdomain'] . '" data-waapi="' . $string['API_key'] . '" data-watoken="' . $string['token'] . '" data-adminemail="' . $string['AMO_admin_email'] . '" data-adminkey="' . $string['AMO_API_key'] . '" value="ПРИКРЕПИТЬ">') : ('Прикреплены');
    echo '</td>';
//        echo        '<td><input type="button" class="tool-btn qr-send" data-waapi="'.$string['API_key'].'" data-watoken="'.$string['token'].'" value="ОТПРАВИТЬ"><br>E-mail: <input type="text" id="qr-email"></td>';
//        echo    '<td>' .
//                    '<img class="edit-tool" id="' . $string['request_id'] . '" width="20px;" style="margin-right: 20px;" src="images/edit.png">' .
//                    '<img class="delete-tool" id="' . $string['request_id'] . '" width="20px;" src="images/delete.png">' .
//                '</td>';
//        echo '</tr>';
}
echo '</table><br><br>';

echo '<h3 style="color: #003066">Пользователи с просроченным платежом</h3>';

echo '<table id="table-requests">';
echo '<tr id="header">';
echo '<td>№ заявки</td>';
echo '<td>Имя</td>';
echo '<td>Телефон</td>';
echo '<td>Субдомен</td>';
echo '<td>Время заявки</td>';
echo '<td>Время окончания действия виджета</td>';
echo '<td>Статус</td>';
echo '<td>API ключ</td>';
echo '<td>Токен</td>';
echo '<td>Google аккаунт</td>';
echo '<td>Google пароль</td>';
echo '<td>AMO логин админа</td>';
echo '<td>AMO API ключ админа</td>';
//echo        '<td>Комментарий</td>';
echo '<td>Вебхуки (должны быть заполнены субдомен, API ключ, токен, AMO логин админа, AMO API ключ админа)</td>';
echo '<td>QR-код (должны быть заполнены API ключ, токен)</td>';
echo '<td>' . $tool_img . '</td>';
echo '</tr>';

foreach ($table_overdue as $string) {
    echo '<tr>';
    echo '<td>' . $string['request_id'] . '</td>';
    echo '<td>' . $string['client_name'] . '</td>';
    echo '<td>' . $string['client_number'] . '</td>';
    echo '<td>' . $string['subdomain'] . '</td>';
    echo '<td nowrap>' . $string['date_request'] . '</td>';
    echo '<td>' . $string['date_access'] . '</td>';
    echo '<td>' . $string['state'] . '</td>';
    echo '<td>' . $string['API_key'] . '</td>';
    echo '<td>' . $string['token'] . '</td>';
    echo '<td>' . $string['google_account'] . '</td>';
    echo '<td>' . $string['google_pasword'] . '</td>';
    echo '<td>' . $string['AMO_admin_email'] . '</td>';
    echo '<td>' . $string['AMO_API_key'] . '</td>';
//    echo    '<td>' . $string['comment'] . '</td>';
    echo '<td>';
    echo ($string['webhooks'] == 0) ? ('<input type="button" class="tool-btn wh-on" data-subdomain="' . $string['subdomain'] . '" data-waapi="' . $string['API_key'] . '" data-watoken="' . $string['token'] . '" data-adminemail="' . $string['AMO_admin_email'] . '" data-adminkey="' . $string['AMO_API_key'] . '" value="ПРИКРЕПИТЬ">') : ('Прикреплены');
    echo '</td>';
    echo '<td><input type="button" class="tool-btn qr-send" data-waapi="' . $string['API_key'] . '" data-watoken="' . $string['token'] . '" value="ОТПРАВИТЬ"><br>E-mail: <input type="text" id="qr-email"></td>';
    echo '<td>' .
        '<img class="edit-tool" id="' . $string['request_id'] . '" width="20px;" style="margin-right: 20px;" src="images/edit.png">' .
        '<img class="delete-tool" id="' . $string['request_id'] . '" width="20px;" src="images/delete.png">' .
        '</td>';
    echo '</tr>';
}
echo '</table><br><br>';
echo '</table><br><br>';
echo '</div>';


?>
    <script type="text/javascript" src="main.js"></script>
<?php


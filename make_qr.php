<?php
require __DIR__.'/PHPMailer/src/PHPMailer.php';

// URL для запроса GET /messages
$url = $_POST['wa_api'] . 'qr_code?token=' . $_POST['wa_token'];
$file = file_get_contents($url); // Отправим запрос

$mail = new PHPMailer;
$mail->setFrom('email@example.com', 'Widget WhatsApp');
$mail->addAddress($_POST['email'], '');
$mail->Subject = 'Qrcode WhatsApp';
$mail->msgHTML("Сканирование QR-кода:<br>1. Откройте WhatsApp на телефоне;<br>2. Нажмите Настройки->WhatsApp WEB и добавить;<br>3. Просканируйте код;<br>4. Держите телефон подключенным к интернету.<br><br>Код можно сканировать в течение 1 минуты после отправки письма. Виджет будет доступен после сканирования кода.");
// Attach uploaded files
$mail->addStringAttachment($file,'qrcode.png');
$r = $mail->send();

echo 'status: '.$r;
?>
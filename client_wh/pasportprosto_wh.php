<?php
$data_json = file_get_contents('php://input');
$subdomain = '';
$admin_email = '';
$admin_api = '';
$post_url = 'https://example.org/dashboard/whatsapp_widget/wa_webhooks.php';


//fwrite(fopen("logs_wa_messages.txt", "a"), "\n".date('d.m.Y	(H:i:s)')." - ".print_r('lalala3037',true) . "\n-------\n");
//fwrite(fopen("logs_wa_messages.txt", "a"), "\n".date('d.m.Y	(H:i:s)')." - ".print_r(json_decode($data_json),true) . "\n-------\n");

////////////////////////////////////
$post_array = array(
'subdomain' => $subdomain,
'admin_email' => $admin_email,
'admin_api' => $admin_api,
'data_json' => $data_json
);

$post_query = http_build_query($post_array);
$options = array('http' => array(
'method' => 'POST',
'header' => 'Content-type: application/x-www-form-urlencoded',
'content' => $post_query
)
);

$context = stream_context_create($options);
$result = file_get_contents($post_url, false, $context);

fwrite(fopen("logs_wa_messages_3.txt", "a"), "\n".date('d.m.Y	(H:i:s)')." - ".print_r($post_array,true) . "\n-------\n");


//echo "<pre>";
//print_r(json_decode($result));
//echo "</pre>";
?>
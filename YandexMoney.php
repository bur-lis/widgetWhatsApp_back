<?php
include_once 'init/wa_db_init.php';
header('Content-Type: text/json; charset=utf-8');
global $secret_key;
$secret_key = 123454321;

$subdomain = $_REQUEST["label"];
// echo $subdomain;
    
$date = date("Y-m-d H:i:s");
$date_access = new DateTime($date);

$query = "SELECT `date_access`, `state` FROM wa_requests WHERE subdomain=?";
$table_id = $Database_req->getRows($query,[$subdomain]);
$row = $table_id[0];
$current_date_access = $row['date_access'];
$state = $row['state'];

if ($current_date_access < $date || $state == 'test') {
    // echo 1;
    $date_access = new DateTime($date);
    include 'conectChatApi.php';
} else {
    // echo 2;
    $date_access = new DateTime($current_date_access);
}

// print_r($_REQUEST);
switch ($_REQUEST['amount']) {
    case '1.96':
    case '1.99':
        $date_access->add(new DateInterval('P1M'));
        break;
    case '7164':
    case '7056':
        $date_access->add(new DateInterval('P3M'));
        break;
    case '13731':
    case '13525':
        $date_access->add(new DateInterval('P6M'));
        break;
    case 26000:
        $date_access->add(new DateInterval('P1Y'));
        break;
    default:
        $date_access->sub(new DateInterval('P1M'));
}

// echo $date_access->format("Y-m-d H:i:s");
// echo $subdomain;


$query = "UPDATE wa_requests SET date_access=?, state=? WHERE subdomain=?";
$condition = [$date_access->format("Y-m-d H:i:s"), 'used', $subdomain];
$Database_req->updateRow($query, $condition);
?>
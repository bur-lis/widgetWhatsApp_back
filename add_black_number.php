<?php
header('Access-Control-Allow-Origin: *');
include_once 'init/wa_db_init.php';

$query = "SELECT * FROM wa_black_list WHERE (subdomain=?) AND (number=?) LIMIT 1";
$condition = [$_POST['subdomain'],$_POST['number']];
$number_table = $Database_wa->getRows($query,$condition);

if($number_table[0]['id']) {
    echo json_encode("0");
}
else {
    $query = "INSERT INTO wa_black_list SET subdomain=?, number=?";
    $condition = [$_POST['subdomain'],$_POST['number']];
    $Database_wa->insertRow($query,$condition);
    echo json_encode("1");
}
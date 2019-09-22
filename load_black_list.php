<?php
header('Access-Control-Allow-Origin: *');
include_once 'init/wa_db_init.php';

$query = "SELECT number FROM wa_black_list WHERE subdomain=?";
$condition = [$_POST['subdomain']];
$numbers_table = $Database_wa->getRows($query,$condition);

$numbers_arr = [];
foreach ($numbers_table as $string) {
    array_push($numbers_arr,$string['number']);
}

echo json_encode($numbers_arr);
<?php
header('Access-Control-Allow-Origin: *');
include_once 'init/wa_db_init.php';

$query = "DELETE FROM wa_black_list WHERE (subdomain=?) AND (number=?)";
$condition = [$_POST['subdomain'],$_POST['number']];
$Database_wa->deleteRow($query,$condition);

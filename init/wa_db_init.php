<?php

require __DIR__ . '/Database.php';
include __DIR__ . '../passwords.php';
$Database_wa = new Database( $DB_wapp['name'], $DB_wapp['pass'], $DB_wapp['host'], $DB_wapp['last']);
$Database_req = new Database( $DB_leads['name'], $DB_leads['pass'], $DB_leads['host'], $DB_leads['last']);
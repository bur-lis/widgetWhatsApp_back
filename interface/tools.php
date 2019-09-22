<?php
include_once '../../app/db_init.php';

    if(isset($_POST['tool'])) {
        $tool = $_POST['tool'];
        if($tool==='get_edit_data') {
            $id = $_POST['id'];

            $query = "SELECT * FROM wa_requests WHERE request_id=? LIMIT 1";
            $condition = [$id];
            $table = $Database->getRows($query,$condition);

            $data = ['name'   => $table[0]['client_name'],
                     'number' => $table[0]['client_number'],
                     'state'  => $table[0]['state'],
                     'api'    => $table[0]['API_key'],
                     'token'  => $table[0]['token'],
                     'google_acc' => $table[0]['google_account'],
                     'google_pass' => $table[0]['google_pasword'],
                     'amo_admin_login' => $table[0]['AMO_admin_email'],
                     'amo_admin_api' => $table[0]['AMO_API_key'],
                     'comment' => $table[0]['comment'],
                     'webhooks' => $table[0]['webhooks']];

            echo json_encode($data);
        }
//////////////////////////////////////
        if($tool==='safe_edit_data') {
            $values = $_POST['values'];

            $query = "UPDATE wa_requests SET client_name=?,client_number=?,state=?,API_key=?,token=?,google_account=?,google_pasword=?,AMO_admin_email=?,AMO_API_key=?,comment=? WHERE request_id=?";
            $Database->updateRow($query,$values);
        }

////////////////////////////////////////
        if($tool==='safe_delete_data') {
            $id = $_POST['id'];

            $query = "DELETE FROM wa_requests WHERE request_id=?";
            $condition = [$id];
            $Database->deleteRow($query,$condition);
        }
    }
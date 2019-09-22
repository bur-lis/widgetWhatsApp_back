<?php
require_once __DIR__ . '/../../app/classes/autoloader.php';
include_once __DIR__ . '/../../dop/lib/db/Database.php';

$Database_wa = new Database( '', '', '', '');

$amo = new \AmoCRM\Client('', '', '');

$data = json_decode(file_get_contents('php://input'), true);

foreach ($data['messages'] as $message) {
    $black = $Database_wa->getRows("SELECT * FROM wa_black_list WHERE subdomain = ?", ['mkan']);
    if(count(explode("-", $message["chatId"])) > 1){
        continue;
    }
    fwrite(fopen("logs.txt", "a"), "\n" . date('d.m.Y	(H:i:s)') . " - " . print_r($message, true) . "\n");

    $phone = explode("@", $message["chatId"])[0];
    foreach ($black as $line){
        if($line['number'] == mb_substr( $phone, 1)){
            continue;
        }
    }

    $contact = getContact($phone);
    $lead_id = null;
    if($contact != null){
        $contact_id = $contact["id"];
        $lead_id = getLeadId($contact);
    }

    if ($message["fromMe"] == 1) {
        checkAndCreate($lead_id, $contact_id, $phone, $message["senderName"]);
        $title = "Сообщение в WhatsApp (+" . $phone . ") отправлено:\n";
    } else {
        checkAndCreate($lead_id, $contact_id, $phone, $message["senderName"]);
        $title = "Получено сообщение WhatsApp (+" . $phone . "):\n";
        //makeTask($lead_id, "Ответить на сообщение WA\n");
    }
    if ($contact_id != null) {
        makeNote($contact_id, $title . $message["body"]);
    }
}

function checkAndCreate(&$lead_id, &$contact_id, $phone, $name)
{
    global $amo;
    $flag = false;
    if ($lead_id == null) {
        $flag = true;
        $lead = $amo->lead;
        $lead['name'] = 'Заявка из WhatsApp';
        $lead['date_create'] = 'now';
        $lead['status_id'] = 11996545;
        $lead_id = $lead->apiAdd();
    }
    if ($contact_id == null) {
        $contact = $amo->contact;
        $contact['name'] = $name;
        $contact['linked_leads_id'] = $lead_id;
        $contact->addCustomField(981806, [
            [$phone, 'MOB'],
        ]);
        $contact_id = $contact->apiAdd();
    } else if ($flag) {
        $contact = $amo->contact;
        $contact['linked_leads_id'] = $lead_id;
        $contact->apiUpdate($contact_id);
    }
}

function getContact($phone)
{
    global $amo;
    $amoRequest = $amo->contact->apiList([
        'query' => mb_substr($phone, 1)
    ]);
    return !empty($amoRequest) ? $amoRequest[0] : null;
}

function getLeadId($contact)
{
    if (count($contact['linked_leads_id']) > 0) {
        return $contact['linked_leads_id'][0];
    }
    return null;
}

function makeNote($contact_id, $message)
{
    global $amo;
    $note = $amo->note;
    $note['element_id'] = $contact_id;
    $note['element_type'] = \AmoCRM\Models\Note::TYPE_CONTACT; // 1 - contact, 2 - lead
    $note['note_type'] = \AmoCRM\Models\Note::COMMON; // @see //developers.amocrm.ru/rest_api/notes_type.php
    $note['text'] = $message;
    $result = $note->apiAdd();
    fwrite(fopen("test.txt", "a"), "\n" . date('d.m.Y	(H:i:s)') . " contact_id: " . $contact_id . " - " . $message . "\n");
}

function makeTask($lead_id, $message)
{
    global $amo;
    $task = $amo->task;
    $task['element_id'] = $lead_id;
    $task["complete_till_at"] = time();
    $task['element_type'] = \AmoCRM\Models\Note::TYPE_LEAD; // 1 - contact, 2 - lead
    $task['task_type'] = 3;
    $task['text'] = $message;
    $task->apiAdd();
}
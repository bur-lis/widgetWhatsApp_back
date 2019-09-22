<?php
require_once __DIR__ . '/../app/classes/autoloader.php';
include_once __DIR__ . '/../dop/lib/db/Database.php';

$Database_wa = new Database('', '', '', '');
$amo = new \AmoCRM\Client($_POST['subdomain'], $_POST['admin_email'], $_POST['admin_api']);

$data = json_decode($_POST['data_json'], true);

file_put_contents('../whatsapp_widget_lis/logs/log.json', "eiiee   ");

foreach ($data['messages'] as $message) {
    $black = $Database_wa->getRows("SELECT * FROM wa_black_list WHERE subdomain = ?", [$_POST['subdomain']]);
    if (count(explode("-", $message["chatId"])) > 1) {
        continue;
    }
    $phone = explode("@", $message["chatId"])[0];

    foreach ($black as $line) {
        if ($line['number'] == mb_substr($phone, 1)) {
            continue;
        }
    }

    $contact = getContact($phone);
    $contact_id = $contact["id"];
    $lead_id = getLeadId($contact);

    file_put_contents('../whatsapp_widget_lis/logs/log.json', $lead_id, FILE_APPEND);
    file_put_contents('../whatsapp_widget_lis/logs/log.json',$_POST['subdomain'],FILE_APPEND);

    $contact_name = $contact["name"];
    if ($_POST['subdomain'] == 'skyap') {

        if ($message["fromMe"] == 1) {
            checkAndCreate($lead_id, $contact_id, $phone, $message["senderName"]);
            $title = "Сообщение в WhatsApp ОТПРАВЛЕНО контакту " . $contact_name . " (+" . $phone . "):\n";
        } else {
            checkAndCreate($lead_id, $contact_id, $phone, $message["senderName"]);
            $title = "Сообщение в WhatsApp ПОЛУЧЕНО от контакта " . $contact_name . " (+" . $phone . "):\n";
        }
    }
    if($_POST['subdomain']=='1343328msk'){
        if ($message["fromMe"] == 1) {
            $title = "Сообщение в WhatsApp ОТПРАВЛЕНО контакту " . $contact_name . " (+" . $phone . "):\n";
        } else {
            $title = "Сообщение в WhatsApp ПОЛУЧЕНО от контакта " . $contact_name . " (+" . $phone . "):\n";
        }
    }
    file_put_contents('../whatsapp_widget_lis/logs/log.json', $contact_id, FILE_APPEND);

    if ($contact_id != null) {
        makeNote($contact_id, $title . $message["body"]);
        file_put_contents('../whatsapp_widget_lis/logs/log.json', "  cotact_id ", FILE_APPEND);

    }
}

function getContact($phone)
{
    global $amo;
    $phone = preg_replace("/[^,.0-9]/", '', $phone);
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
}

function checkAndCreate(&$lead_id, &$contact_id, $phone, $name)
{
    global $amo;
    $flag = false;
    if ($lead_id == null) {
        $flag = true;
        $lead = $amo->lead;
        $lead['name'] = 'Заявка из WhatsApp ' . $phone;
        $lead['date_create'] = 'now';
//        $lead['status_id'] = 11996545;
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
<?php
ini_set("display_errors", 'on');
error_reporting(E_ALL);
require_once __DIR__ . '/../app/classes/autoloader.php';
include_once __DIR__ . '/../dop/lib/db/Database.php';
include __DIR__ . '../passwords.php';
$Database_wa = new Database( $DB_wapp['name'], $DB_wapp['pass'], $DB_wapp['host'], $DB_wapp['last']);

$amo = new \AmoCRM\Client($_POST['subdomain'], $_POST['admin_email'], $_POST['admin_api']);

$data = json_decode($_POST['data_json'], true);

foreach ($data['messages'] as $message) {

    if (count(explode("-", $message["chatId"])) > 1) {
        continue;
    }

    $phone = explode("@", $message["chatId"])[0];

    $contact = getContact($phone);

    $contact_id = $contact["id"];
    $lead_id = getLeadId($contact);


    $contact_name = $contact["name"];
    //file_put_contents('logs/log1.json', $lead_id . ' - ' . $contact_id . ' - ' . $phone. ' - ' . $message["senderName"]);

    if ($message["fromMe"] == 1) {
        // file_put_contents('logs/log1.json','root: ' . $phone . ' - ' . $contact_id. ' - ' . $contact_name . '\n');
        checkAndCreate($lead_id, $contact_id, $phone, $message["senderName"]);
        $title = "Сообщение в WhatsApp ОТПРАВЛЕНО контакту " . $contact_name . " (+" . $phone . "):\n";
    } else {
        checkAndCreate($lead_id, $contact_id, $phone, $message["senderName"]);
        $title = "Сообщение в WhatsApp ПОЛУЧЕНО от контакта " . $contact_name . " (+" . $phone . "):\n";
    }
    // file_put_contents('logs/log1.json', "brrr" );

    if ($contact_id != null) {
        // file_put_contents('logs/log1.json', "mmmmmmmmm" );
        makeNote($contact_id, $title . $message["body"]);
        //file_put_contents('logs/log1.json', $contact_id. $title . $message["body"] );

    }
}


function getContact($phone)
{

    global $amo;
    $phone = preg_replace("/[^,.0-9]/", '', $phone);
    // file_put_contents('logs/log1.json', mb_substr($phone, 1));
    $parametrs = [
        'query' => mb_substr($phone, 1)
    ];

    // file_put_contents('logs/log1.json',  get_class_methods($amo->contact), FILE_APPEND);
    $amoRequest = $amo->contact->apiList($parametrs);

    // file_put_contents('logs/log1.json', "Lala", FILE_APPEND);


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
    echo $contact_id;

    echo $name;
    echo $lead_id;

    global $amo;
    $flag = false;
    file_put_contents('logs/log1.json', 'c&c: ' . $lead_id . ' - ' . $contact_id . '\n', FILE_APPEND);
    if ($lead_id == null) {
        file_put_contents('logs/log1.json', "mmmm  yyy\n", FILE_APPEND);
        $flag = true;
        $lead = $amo->lead;
        $lead['name'] = 'Заявка из WhatsApp ' . $phone;
        $lead['date_create'] = 'now';
//        $lead['status_id'] = 11996545;
        $lead_id = $lead->apiAdd();

    }
    if ($contact_id == null) {
        file_put_contents('logs/log1.json', ";;;  ttt\n", FILE_APPEND);
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
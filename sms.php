<?php

use Modules\Messages\Sender\Models\Senders\SmsSender;

use Modules\Messages\Sender\Models\Senders\WhatsAppSender;
use Modules\WhatsApp\Models\WhatsApp;
use System\Main\Core;
use System\Utils\Utils;

include_once __DIR__ . "/../../msk/dashboard/autoloader.php";
include_once 'init/wa_db_init.php';

Core::init();

//$sender = new SmsSender();

$sender = new WhatsAppSender();

$outAdress = "widget";

$sender->setWhatsApp(new WhatsApp($outAdress));
$phone = "89111140877";
$sender->setAdress($phone);
$sender->setText("Здравствуйте, Вы оставляли заявку в на Виджет WhatsApp для вашей amoCRM системы");
Utils::echoPre($sender->sendMessage());


// $query = "SELECT client_number,client_name, subdomain  FROM wa_requests WHERE state = 'start' OR state = 'delete'";
// $rows = $Database_req->getRows($query);
// Utils::echoPre($rows);
// foreach ($rows as $row){
//     $sender->setText("Здравствуйте" .change_name(). ", Вы оставляли заявку в на Виджет WhatsApp для вашей amoCRM системы (" .$row['subdomain'].".amocrm.ru).");
//     Utils::echoPre($row);
//     $phone = $row['client_number'];
//     $sender->setAdress($phone);
//     // Utils::echoPre($sender->sendMessage());
//     // $query = "DELETE FROM wa_requests WHERE client_number =" . $phone;
//     // $mm = $Database_req->insertRow($query);
// }

function change_name($name_str)
{
    $lead_name = "";
    $name_str = mb_convert_case($name_str, MB_CASE_LOWER);
    $name_str = preg_replace("/[^а-я\s]/iu", "", $name_str);
    $name_list = ['Аарон', 'Абрам', 'Аваз', 'Аввакум', 'Август', 'Августа', 'Авдей', 'Авраам', 'Аврора', 'Автандил', 'Агап', 'Агата', 'Агафон', 'Агафья', 'Аггей', 'Аглая', 'Агнесса', 'Агния', 'Агриппина', 'Агунда', 'Ада', 'Адам', 'Аделина', 'Адель', 'Адиля', 'Адис', 'Адольф', 'Адриан', 'Адриана', 'Аза', 'Азалия', 'Азарий', 'Азат', 'Азиза', 'Аида', 'Айгуль', 'Айжан', 'Айрат', 'Акакий', 'Аким', 'Аксинья', 'Акулина', 'Алан', 'Алана', 'Алевтина', 'Александр', 'Александра', 'Алексей', 'Алёна', 'Алена', 'Али', 'Алико', 'Алина', 'Алиса', 'Алихан', 'Алия', 'Алла', 'Алоиз', 'Алсу', 'Альберт', 'Альберта', 'Альбина', 'Альвина', 'Альжбета', 'Альфия', 'Альфред', 'Альфреда', 'Амадей', 'Амадеус', 'Амаль', 'Амаяк', 'Амвросий', 'Амелия', 'Амина', 'Анастасия', 'Анатолий', 'Анвар', 'Ангел', 'Ангелина', 'Андоим', 'Андре', 'Андрей', 'Анеля', 'Анжела', 'Аникита', 'Анисья', 'Анита', 'Анна', 'Антон', 'Антонина', 'Ануфрий', 'Ануш', 'Анфиса', 'Аполлинарий', 'Аполлинария', 'Арам', 'Ариадна', 'Ариана', 'Арий', 'Арина', 'Аристарх', 'Аркадий', 'Арман', 'Армен', 'Арно', 'Арнольд', 'Арсений', 'Арслан', 'Артём', 'Артем', 'Артемий', 'Артур', 'Архелия', 'Архип', 'Асия', 'Аскольд', 'Ассоль', 'Астра', 'Астрид', 'Ася', 'Аурелия', 'Афанасий', 'Афанасия', 'Ахмет', 'Ашот', 'Аэлита', 'Беатриса', 'Бежен', 'Белла', 'Бенедикт', 'Бенедикта', 'Берек', 'Береслава', 'Бернар', 'Берта', 'Биргит', 'Бирута', 'Богдан', 'Богдана', 'Боголюб', 'Божена', 'Болеслав', 'Бонифаций', 'Борис', 'Борислав', 'Борислава', 'Боян', 'Бронислав', 'Бронислава', 'Бруно', 'Булат', 'Вадим', 'Валентин', 'Валентина', 'Валерий', 'Валерия', 'Вальтер', 'Ванда', 'Варвара', 'Вардан', 'Варлаам', 'Варфоломей', 'Василий', 'Василина', 'Василиса', 'Вацлав', 'Велизар', 'Велор', 'Венедикт', 'Венера', 'Вениамин', 'Вера', 'Вероника', 'Веселина', 'Весна', 'Веста', 'Вета', 'Вида', 'Викентий', 'Виктор', 'Виктория', 'Вилен', 'Вилли', 'Вилора', 'Вильгельм', 'Виолетта', 'Виргиния', 'Виринея', 'Виссарион', 'Виталий', 'Виталия', 'Витаутас', 'Витольд', 'Владимир', 'Владислав', 'Владислава', 'Владлен', 'Владлена', 'Влас', 'Володар', 'Вольдемар', 'Всеволод', 'Вячеслав', 'Габриэлла', 'Гавриил', 'Галактион', 'Галина', 'Гарри', 'Гастон', 'Гаянэ', 'Гаяс', 'Гевор', 'Геворг', 'Гелена', 'Гелла', 'Геннадий', 'Генриетта', 'Генрих', 'Георгий', 'Георгина', 'Гера', 'Геральд', 'Герасим', 'Герман', 'Гертруда', 'Глафира', 'Глеб', 'Глория', 'Гордей', 'Гордон', 'Горислав', 'Гортензия', 'Градимир', 'Гражина', 'Грета', 'Григорий', 'Гузель', 'Гулия', 'Гульмира', 'Гульназ', 'Гульнара', 'Гурий', 'Густав', 'Давид', 'Дайна', 'Далия', 'Дамир', 'Дамира', 'Дана', 'Даниил', 'Даниэла', 'Данияр', 'Данута', 'Дарина', 'Дарья', 'Дебора', 'Демид', 'Демьян', 'Денис', 'Джамал', 'Джамиля', 'Джемма', 'Джереми', 'Джордан', 'Джулия', 'Джульетта', 'Диана', 'Дилара', 'Диля', 'Дина', 'Динара', 'Динасий', 'Диодора', 'Дионисия', 'Дмитрий', 'Добрыня', 'Доля', 'Доминика', 'Дональд', 'Донат', 'Донатос', 'Дора', 'Дорофей', 'Ева', 'Евангелина', 'Евгений', 'Евгения', 'Евграф', 'Евдоким', 'Евдокия', 'Евсей', 'Евстафий', 'Егор', 'Екатерина', 'Елена', 'Елизавета', 'Елизар', 'Елисей', 'Емельян', 'Еремей', 'Ермолай', 'Ерофей', 'Есения', 'Ефим', 'Ефимий', 'Ефимия', 'Ефрем', 'Жан', 'Жанна', 'Жасмин', 'Ждан', 'Жозефина', 'Жорж', 'Забава', 'Заира', 'Замира', 'Зара', 'Зарема', 'Зарина', 'Заур', 'Захар', 'Захария', 'Земфира', 'Зигмунд', 'Зинаида', 'Зиновий', 'Зита', 'Злата', 'Зоя', 'Зульфия', 'Зухра', 'Ибрагим', 'Иван', 'Иванна', 'Иветта', 'Ивона', 'Игнат', 'Игорь', 'Ида', 'Иероним', 'Изабелла', 'Измаил', 'Изольда', 'Израиль', 'Изяслав', 'Илария', 'Илзе', 'Илиан', 'Илиана', 'Илларион', 'Илона', 'Ильхам', 'Илья', 'Ильяс', 'Инара', 'Инга', 'Инесса', 'Инна', 'Иннокентий', 'Иоанна', 'Иоланта', 'Ион', 'Ионос', 'Иосиф', 'Ипполит', 'Ираида', 'Ираклий', 'Ирена', 'Иржи', 'Ирина', 'Ирма', 'Исаак', 'Исай', 'Исидор', 'Исидора', 'Искандер', 'Июлия', 'Ия', 'Казимир', 'Калерия', 'Камилла',

        'Камиль', 'Капитолина', 'Карен', 'Карим', 'Карима', 'Карина', 'Карл', 'Каролина', 'Катарина', 'Ким', 'Кира', 'Кирилл', 'Кирилла', 'Клавдий', 'Клавдия', 'Клара', 'Кларисса', 'Клаус', 'Клемент', 'Клим', 'Климентина', 'Клод', 'Кондрат', 'Конкордий', 'Конрад', 'Константин', 'Констанция', 'Кора', 'Корней', 'Корнилий', 'Кристина', 'Ксанф', 'Ксения', 'Кузьма', 'Лаврентий', 'Лада', 'Лазарь', 'Лайма', 'Лана', 'Лариса', 'Лаура', 'Лев', 'Леван', 'Левон', 'Лейла', 'Лейсан', 'Ленар', 'Леокадия', 'Леон', 'Леонард', 'Леонид', 'Леонида', 'Леонтий', 'Леопольд', 'Леся', 'Лиана', 'Лидия', 'Лилиана', 'Лилия', 'Лина', 'Линда', 'Лия', 'Лола', 'Лолита', 'Луиза', 'Лука', 'Лукерья', 'Лукьян', 'Любим', 'Любовь', 'Любомила', 'Любомир', 'Людвиг', 'Людмила', 'Люсьен', 'Люция', 'Магда', 'Магдалина', 'Мадина', 'Мадлен', 'Май', 'Майя', 'Макар', 'Максим', 'Максимилиан', 'Максуд', 'Малика', 'Мальвина', 'Мансур', 'Мануил', 'Марат', 'Маргарита', 'Мариан', 'Марианна', 'Марика', 'Марина', 'Мария', 'Марк', 'Марселина', 'Марсель', 'Марта', 'Мартин', 'Марьяна', 'Марфа', 'Марьям', 'Матвей', 'Матильда', 'Махмуд', 'Мелания', 'Мелисса', 'Мераб', 'Мефодий', 'Мечеслав', 'Мила', 'Милада', 'Милан', 'Милана', 'Милена', 'Милица', 'Милолика', 'Милослава', 'Мира', 'Мирдза', 'Мирон', 'Мирослав', 'Мирослава', 'Мирра', 'Митрофан', 'Михаил', 'Михайлина', 'Михаэла', 'Мичлов', 'Модест', 'Моисей', 'Моника', 'Мстислав', 'Муза', 'Мурат', 'Муслим', 'Надежда', 'Назар', 'Назарий', 'Назира', 'Наиль', 'Наиля', 'Нана', 'Наталия', 'Наталья', 'Натан', 'Нателла', 'Наум', 'Нелли', 'Неонила', 'Нестор', 'Ника', 'Никанор', 'Никита', 'Никифор', 'Никодим', 'Николай', 'Николь', 'Никон', 'Нильс', 'Нина', 'Нинель', 'Нисон', 'Нифонт', 'Нонна', 'Нора', 'Норманн', 'Овидий', 'Одетта', 'Оксана', 'Олан', 'Олег', 'Олесь', 'Олеся', 'Оливия', 'Ольга', 'Онисим', 'Орест', 'Осип', 'Оскар', 'Остап', 'Офелия', 'Павел', 'Павла', 'Памела', 'Панкрат', 'Парамон', 'Патриция', 'Пелагея', 'Петр', 'Пимен', 'Платон', 'Полина', 'Порфирий', 'Потап', 'Прасковья', 'Прокофий', 'Прохор', 'Равиль', 'Рада', 'Радий', 'Радмила', 'Радосвета', 'Раис', 'Раиса', 'Раймонд', 'Рамиз', 'Рамиль', 'Расим', 'Ратибор', 'Ратмир', 'Рафаил', 'Рафик', 'Рашид', 'Ревекка', 'Регина', 'Рем', 'Рема', 'Рената', 'Ренольд', 'Римма', 'Ринат', 'Рифат', 'Рихард', 'Ричард', 'Роберт', 'Роберта', 'Родион', 'Роза', 'Роксана', 'Ролан', 'Роман', 'Ростислав', 'Ростислава', 'Рубен', 'Рудольф', 'Рузанна', 'Рузиля', 'Румия', 'Руслан', 'Руслана', 'Рустам', 'Руфина', 'Рушан', 'Сабина', 'Сафира', 'Сабир', 'Сабрина', 'Савва', 'Савелий', 'Саид', 'Саида', 'Саломея', 'Самсон', 'Самуил', 'Сандра', 'Сания', 'Санта', 'Сарра', 'Светлана', 'Святослав', 'Севастьян', 'Северин', 'Северина', 'Селена', 'Семен', 'Серафим', 'Серафима', 'Сергей', 'Сильва', 'Сима', 'Симона', 'Снежана', 'Созия', 'Сократ', 'Соломон', 'Софья', 'Спартак', 'Стакрат', 'Станимир', 'Станислав', 'Станислава', 'Стелла', 'Степан', 'Стефания', 'Стивен', 'Стоян', 'Султан', 'Сусанна', 'Таира', 'Таис', 'Таисия', 'Тала', 'Талик', 'Тамаз', 'Тамара', 'Тарас', 'Татьяна', 'Тельман', 'Теодор', 'Теодора', 'Тереза', 'Терентий', 'Тибор', 'Тигран', 'Тигрий', 'Тимофей', 'Тимур', 'Тина', 'Тит', 'Тихон', 'Томас', 'Томила', 'Трифон', 'Трофим', 'Ульманас', 'Ульяна', 'Урсула', 'Устин', 'Устина', 'Фаддей', 'Фаиза', 'Фаина', 'Фанис', 'Фания', 'Фаня', 'Фарид', 'Фарида', 'Фархад', 'Фатима', 'Фая', 'Фёдор', 'Федор', 'Федот', 'Фекла', 'Феликс', 'Фелиция', 'Феодосий', 'Фердинанд', 'Феруза', 'Фидель', 'Филимон', 'Филипп', 'Флора', 'Флорентий', 'Фома', 'Франсуаза', 'Франц', 'Фредерика', 'Фрида', 'Фридрих', 'Фуад', 'Харита', 'Харитон', 'Хильда', 'Христиан', 'Христина', 'Христос', 'Христофор', 'Христя', 'Цветана', 'Цезарь', 'Цецилия', 'Чеслав', 'Чеслава', 'Чингиз', 'Чулпан', 'Шакира', 'Шамиль', 'Шарлотта', 'Шерлок', 'Эвелина', 'Эдвард', 'Эдгар', 'Эдда', 'Эдита', 'Эдмунд', 'Эдуард', 'Элеонора', 'Элиза', 'Элина', 'Элла', 'Эллада', 'Элоиза', 'Эльвира', 'Эльга', 'Эльдар', 'Эльза', 'Эльмира', 'Эля', 'Эмилия', 'Эмиль', 'Эмин', 'Эмма', 'Эммануил', 'Эраст', 'Эрик', 'Эрика', 'Эрнест', 'Эрнестина', 'Эсмеральда', 'Этери', 'Юзефа', 'Юлиан', 'Юлий', 'Юлия',

        'Юна', 'Юния', 'Юнона', 'Юрий', 'Юханна', 'Юхим', 'Ядвига', 'Яким', 'Яков', 'Ян', 'Яна', 'Янита', 'Янка', 'Януарий', 'Ярина', 'Яромир', 'Ярослав', 'Ярослава', 'Ясон'];
    foreach ($name_list as $name) {
        if (mb_stripos(" " . $name_str . " ", " " . $name . " ") !== false) {
            return $name;
            break;
        }
    }
    return "";
}


<?php
#Массив с параметрами, которые нужно передать методом POST к API системы
$user=array(
    'USER_LOGIN'=>'name@example.com', #Ваш логин (электронная почта)
    'USER_HASH'=> '' #Хэш для доступа к API (смотрите в профиле пользователя)
);
$subdomain='example'; #Наш аккаунт - поддомен
#Формируем ссылку для запроса
$link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Вы также
можете
использовать и кроссплатформенную программу cURL, если вы не программируете на PHP. */
$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
curl_close($curl); #Завершаем сеанс cURL
/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
$code=(int)$code;
fwrite(fopen("amo_auth_status.txt", "a"), "\n".date('d.m.Y	(H:i:s)')." - ".$code . "\n-------\n");

//echo $code;

/*
 Данные получаем в формате JSON, поэтому, для получения читаемых данных,
 нам придётся перевести ответ в формат, понятный PHP
 */
$Response=json_decode($out,true);
$Response=$Response['response'];
if(isset($Response['auth'])) #Флаг авторизации доступен в свойстве "auth"
    return 'Авторизация прошла успешно';
return 'Авторизация не удалась';
?>
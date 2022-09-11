<?php
$token = '1791910378:AAFAv8x_fni94HYEZ7T9S8oNM03FxXIHftE';
$website = 'https://api.telegram.org/bot'.$token;

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];

switch($message) {
    case '/start':
        $response = 'Me has iniciado';
        sendMessage($chatId, $response, $website);
        break;
    case '/info':
        $response = 'Hola! Soy @trecno_bot';
        sendMessage($chatId, $response, $website);
        break;
    default:
        $response = 'No te he entendido';
        sendMessage($chatId, $response, $website);
        break;
}

function sendMessage($chatId, $response, $website) {
    $url = $website.'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
}
?>
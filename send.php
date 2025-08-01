<?php 
ini_set('display_errors', 1); 
error_reporting(E_ALL); 
header('Content-Type: application/json'); 

// === Получение данных ===
$fullname = $_POST['fullname'] ?? '';
$phone = $_POST['phone'] ?? '';
$telegram = $_POST['telegram'] ?? '';
$email = $_POST['email'] ?? '';
$productName = $_POST['productName'] ?? '';

// Google fields (опционально)
// $name = $_POST['name'] ?? '';
// $welcome = $_POST['welcome'] ?? '';
// $drinking = $_POST['drinking'] ?? '';
// $stay = $_POST['stay'] ?? '';
// $partnerName = $_POST['partnerName'] ?? '';
// $childName = $_POST['childName'] ?? '';

$success = true;
$errors = [];

// === Отправка в Telegram ===
$telegramToken = "8469386738:AAEZqVpy0g-TVR8YFhJtZT8z3jWDVlNe3Ws";
$chatId = "1847244710";

$telegramMessage = "💌 *Заказ Welcome-to-day*\n\n";
$telegramMessage .= "*Имя:* " . $fullname . "\n";
$telegramMessage .= "*Телефон:* " . $phone . "\n";
$telegramMessage .= "*Telegram:* " . $telegram . "\n";
$telegramMessage .= "*Почта:* " . $email . "\n";
$telegramMessage .= "*Шаблон:* " . $productName;

$telegramData = [
    'chat_id' => $chatId,
    'text' => $telegramMessage,
    'parse_mode' => 'Markdown'
];

$context = stream_context_create([
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded",
        'method'  => 'POST',
        'content' => http_build_query($telegramData)
    ]
]);

$telegramResponse = @file_get_contents("https://api.telegram.org/bot$telegramToken/sendMessage", false, $context);
if ($telegramResponse === false) {
    $success = false;
    $errors[] = "Ошибка при отправке в Telegram.";
}

// === Отправка в Google Sheets ===
// $googleScriptUrl = "https://script.google.com/macros/s/AKfycbyyEYNw1C5Uuu40f45CKo0QT6p52V0eK-wKThqz7RUBDft-xHmiXYGwAoBQZYL8ifx7gg/exec";
// $googleParams = [
//     'name' => $name,
//     'welcome' => $welcome,
//     'drinking' => $drinking,
//     'stay' => $stay,
//     'partnerName' => $partnerName,
//     'childName' => $childName
// ];

// $googleResponse = @file_get_contents($googleScriptUrl . '?' . http_build_query($googleParams));
// if ($googleResponse === false) {
//     $success = false;
//     $errors[] = "Ошибка при отправке в Google Таблицы.";
// }

// === Ответ для фронта ===
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Спасибо! Ваш ответ получен!']);
} else {
    echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
}
exit;
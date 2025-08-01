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


$success = true;
$errors = [];

// === Отправка в Telegram ===
$telegramToken = "8469386738:AAEZqVpy0g-TVR8YFhJtZT8z3jWDVlNe3Ws";
$chatId = "7293309046";

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


// === Ответ для фронта ===
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Спасибо! Ваш ответ получен!']);
} else {
    echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
}
exit;
<?php

// Защита от поиска файла в браузере
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}


// Показывать ошибки (только на этапе разработки)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Установка заголовка для JSON-ответа
header('Content-Type: application/json');

// Получаем данные из формы
$name = $_POST['fullname'] ?? '';
$name = $_POST['phone'] ?? '';
$stay = $_POST['telegram'] ?? '';
$partnerName = $_POST['email'] ?? '';

// Флаг успешной отправки
$success = true;
$errors = [];

// // === 1. ОТПРАВКА НА ПОЧТУ ===
// $to = "ritamenshikova47@gmail.com";
// $subject = "RSVP-ответ от $name | Свадьба";

// // HTML-письмо
// $message = "
// <html><head><meta charset='UTF-8'><style>
//     body { font-family: Arial, sans-serif; color: #333; }
//     h2 { color:rgb(38, 195, 95); } p { margin: 5px 0; }
// </style></head><body>
//     <h2>RSVP-ответ</h2>
//     <p><strong>Имя:</strong> $name</p>
//     <p><strong>Присутствие:</strong> $welcome</p>
//     <p><strong>Алкоголь:</strong> $drinking</p>
//     <p><strong>Второй день:</strong> $stay</p>
//     <p><strong>Партнёр:</strong> $partnerName</p>
//     <p><strong>Дети:</strong> $childName</p>
// </body></html>";

// $headers  = "MIME-Version: 1.0\r\n";
// $headers .= "Content-type: text/html; charset=UTF-8\r\n";
// $headers .= "From: noreply@ywelcome-to-day.ru\r\n";
// $headers .= "Reply-To: noreply@ywelcome-to-day.ru\r\n";

// // Отправка письма
// if (!mail($to, $subject, $message, $headers)) {
//     $success = false;
//     $errors[] = "Не удалось отправить.";
// }

// === 2. ОТПРАВКА В TELEGRAM ===
$telegramToken = "8469386738:AAEZqVpy0g-TVR8YFhJtZT8z3jWDVlNe3Ws";
$chatId = "1847244710";

$telegramMessage = "💌 *Запрос от Welcome-to-day*\n\n";
$telegramMessage .= "*Имя:* " . addslashes($fullname) . "\n";
$telegramMessage .= "*Телефон:* " . addslashes($phone) . "\n";
$telegramMessage .= "*Телеграм:* " . addslashes($telegram) . "\n";
$telegramMessage .= "*Почта:* " . addslashes($email);

$telegramUrl = "https://api.telegram.org/bot$telegramToken/sendMessage";
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

$telegramResponse = file_get_contents($telegramUrl, false, $context);
if ($telegramResponse === false) {
    $success = false;
    $errors[] = "Не удалось отправить.";
}


// // === 3. ОТПРАВКА В GOOGLE SHEETS ===
// $googleScriptUrl = "https://script.google.com/macros/s/AKfycbyyEYNw1C5Uuu40f45CKo0QT6p52V0eK-wKThqz7RUBDft-xHmiXYGwAoBQZYL8ifx7gg/exec";
// $googleParams = [
//     'name' => $name,
//     'welcome' => $welcome,
//     'drinking' => $drinking,
//     'stay' => $stay,
//     'partnerName' => $partnerName,
//     'childName' => $childName
// ];
// $googleResponse = file_get_contents($googleScriptUrl . '?' . http_build_query($googleParams));
// if ($googleResponse === false) {
//     $success = false;
//     $errors[] = "Не удалось отправить.";
// }


        // === ОТВЕТ ДЛЯ JS ===
if ($success) {
    echo json_encode(['success' => true, 'message' => 'Спасибо! Ваш ответ получен!']);
} else {
    echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
}
exit;

?>
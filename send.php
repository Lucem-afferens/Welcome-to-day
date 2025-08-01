<?php  
// Только POST-запросы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {  
    http_response_code(405);  
    exit('Method Not Allowed');  
}  

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Можно прямо тут для теста вывести что-то простое
exit(json_encode(['success' => true, 'message' => 'Тест прошел успешно']));

// Получение данных из формы и защита
$fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''));  
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));  
$telegram = htmlspecialchars(trim($_POST['telegram'] ?? ''));  
$email = htmlspecialchars(trim($_POST['email'] ?? ''));  
$productName = htmlspecialchars(trim($_POST['productName'] ?? ''));  

$success = true;  
$errors = [];  

// === Валидация ===
if (empty($fullname) || empty($phone) || empty($email)) {
    $success = false;  
    $errors[] = "Пожалуйста, заполните все обязательные поля.";  
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $success = false;
    $errors[] = "Некорректный email.";
}

if (!preg_match('/^\d{10,15}$/', $phone)) {
    $success = false;
    $errors[] = "Телефон должен содержать только цифры (10–15 символов).";
}

// === Отправка в Telegram ===
if ($success) {
    $telegramToken = "8469386738:AAEZqVpy0g-TVR8YFhJtZT8z3jWDVlNe3Ws";  
    $chatId = "1847244710";  

    $telegramMessage = "💌 *Запрос от Welcome-to-day*\n\n";  
    $telegramMessage .= "*Имя:* $fullname\n";  
    $telegramMessage .= "*Телефон:* $phone\n";  
    $telegramMessage .= "*Телеграм:* $telegram\n";  
    $telegramMessage .= "*Почта:* $email\n";  
    $telegramMessage .= "*Шаблон:* $productName";  

    $telegramUrl = "https://api.telegram.org/bot$telegramToken/sendMessage";  
    $telegramData = [  
        'chat_id' => $chatId,  
        'text' => $telegramMessage,  
        'parse_mode' => 'Markdown'  
    ];  

    $context = stream_context_create([  
        'http' => [  
            'header' => "Content-type: application/x-www-form-urlencoded",  
            'method' => 'POST',  
            'content' => http_build_query($telegramData)  
        ]  
    ]);  

    $telegramResponse = file_get_contents($telegramUrl, false, $context);  

    if ($telegramResponse === false) {
        $error = error_get_last();
        $success = false;  
        $errors[] = "Не удалось отправить сообщение в Telegram. " . ($error['message'] ?? '');
    } else {
        $responseData = json_decode($telegramResponse, true);
        if (!$responseData['ok']) {
            $success = false;
            $errors[] = "Telegram ошибка: " . ($responseData['description'] ?? 'неизвестная ошибка');
        }
    }
}

// === Ответ для JS
echo json_encode([  
    'success' => $success,  
    'message' => $success ? 'Спасибо! Ваш ответ получен!' : implode(" ", $errors)  
]);  
exit;  
?>
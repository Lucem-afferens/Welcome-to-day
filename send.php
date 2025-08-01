<?php
// Только POST-запросы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Отображение ошибок (только на время отладки)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Получение данных из формы и защита
$fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''));
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$telegram = htmlspecialchars(trim($_POST['telegram'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$productName = htmlspecialchars(trim($_POST['productName'] ?? ''));

$success = true;
$errors = [];

// === Валидация ===
if (empty($fullname)  empty($phone)  empty($email)) {
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

// Функция экранирования Markdown спецсимволов
function escapeMarkdown($text) {
    $search = ['\\', '_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    $replace = array_map(fn($char) => '\\' . $char, $search);
    return str_replace($search, $replace, $text);
}

// === Отправка в Telegram ===
if ($success) {
    $telegramToken = "8469386738:AAEZqVpy0g-TVR8YFhJtZT8z3jWDVlNe3Ws";
    $chatId = "1847244710";

    $telegramMessage = "💌 *Запрос от Welcome-to-day*\n\n";
    $telegramMessage .= "*Имя:* " . escapeMarkdown($fullname) . "\n";
    $telegramMessage .= "*Телефон:* " . escapeMarkdown($phone) . "\n";
    $telegramMessage .= "*Телеграм:* " . escapeMarkdown($telegram) . "\n";
    $telegramMessage .= "*Почта:* " . escapeMarkdown($email) . "\n";
    $telegramMessage .= "*Шаблон:* " . escapeMarkdown($productName);

    // Функция отправки POST-запроса CURL
    function telegramSendMessage($token, $chatId, $message) {
        $url = "https://api.telegram.org/bot$token/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['ok' => false, 'description' => $error];
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    $responseData = telegramSendMessage($telegramToken, $chatId, $telegramMessage);

    if (!$responseData['ok']) {
        $success = false;
        $errors[] = "Telegram ошибка: " . ($responseData['description'] ?? 'неизвестная ошибка');
    }
}

// === Ответ для JS
echo json_encode([
    'success' => $success,
    'message' => $success ? 'Спасибо! Ваш ответ получен!' : implode(" ", $errors)
]);
exit;
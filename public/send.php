
<?php 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    http_response_code(405); 
    exit('Method Not Allowed'); 
} 
 
ini_set('display_errors', 1); 
error_reporting(E_ALL); 
header('Content-Type: application/json'); 
 
$fullname = htmlspecialchars($_POST['fullname'] ?? ''); 
$phone = htmlspecialchars($_POST['phone'] ?? ''); 
$telegram = htmlspecialchars($_POST['telegram'] ?? ''); 
$email = htmlspecialchars($_POST['email'] ?? ''); 
$productName = htmlspecialchars($_POST['productName'] ?? ''); 
 
$success = true; 
$errors = []; 
 
if (empty($fullname)  empty($phone)  empty($email)) { 
    $success = false; 
    $errors[] = "Заполните обязательные поля."; 
} 
 
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
        $success = false; 
        $errors[] = "Ошибка Telegram: " . error_get_last()['message']; 
    } 
} 
 
echo json_encode([ 
    'success' => $success, 
    'message' => $success ? 'Спасибо! Ваш ответ получен!' : implode(" ", $errors) 
]); 
exit; 
?>
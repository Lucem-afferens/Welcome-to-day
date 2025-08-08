<?php 
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL); 
header('Content-Type: application/json'); 

session_start();

// Получение и чистка данных из формы
$fullname = trim($_POST['fullname'] ?? ''); 
$phone = trim($_POST['phone'] ?? ''); 
$email = trim($_POST['email'] ?? ''); 
$ad = trim($_POST['ad'] ?? ''); 
$productName = trim($_POST['productName'] ?? ''); 
$firstPrice = trim($_POST['firstPrice'] ?? ''); 

// Проверка email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Введите корректный email.'
    ]);
    exit;
}

// Промокод и цена
$discountPrice = 990; 
$discountCode = "katyalegenda"; 
$price = ($ad === $discountCode) ? $discountPrice : $firstPrice;

$success = true; 
$errors = []; 

// Настройки
$adminTelegramToken = '8469386738:AAEZqVpy0g-TVR8YFhJtZT8z3jWDVlNe3Ws'; 
$adminChatId = '7293309046'; 
$fromEmail = 'noreply@welcome-to-day.ru'; 
$siteName = 'Welcome-to-day'; 

// Сообщение для WhatsApp (большое, с деталями)
$whatsappMessage = <<<EOT
Здравствуйте! Спасибо за заказ на сайте welcome-to-day.ru 🎉

Ваш шаблон: «{$productName}»
Предварительная стоимость: $price ₽
Вы указали email: $email

Мы можем кастомизировать шаблон под вас:
— Фото и имена
— Даты, адреса, время
— Программа мероприятия
— Пожелания, подарки, дресс-код
— Контакты, музыка, RSVP-вопросы
— Удалить или добавить блоки

💳 После согласования вы получите ссылку на оплату, а затем — финальную версию сайта.

---
✨ Очень рады, что Вы доверяете нам оформление важного события 💫

📋 Как проходит работа:
1. Вы выбираете шаблон или описываете пожелания
2. Мы делаем демо-версию сайта
3. Утверждаем или вносим правки
4. Подключаем нужные каналы (Telegram, почта и т.д.)
5. После оплаты — вы получаете финальную ссылку

🎨 Что нужно от Вас сейчас:

— Отправьте, что хотите изменить в шаблоне (фото/тексты/музыка/дата/время/адреса и т.д.)
• Музыка
• Фотографии (фотография) виновников(виновника) торжества
• Имена (имя) виновников(виновника) торжества
• Дата проведения мероприятия
• Время проведения мероприятия
• Адреса проведения мероприятия
• Фотографии проведения мероприятия (без Вашей фотографии, и если ее наличие требует шаблон, - возьмем из открытых источников)
• Программа мероприятия
• Пожелания и особеннности организации мероприятия
• Подарки
• Дресс-код
• Вопросы в RSVP-форме
• Контакты ведущего/Ваши
• Текст обращения (по Вашему желанию мы можем создать отдельную ссылку на приглашение для конкретного (конкретных) лица (лиц) - 
  то есть заменить "Дорогие гости!" на "Уважаемые и любимые мама и папа!"/"Уважаемый Виктор Константинович";
  цена каждой отдельной ссылки - 500 рублей)
— Укажите блоки, которые нужно убрать
— Укажите блоки, которые хотите добавить (из других шаблонов)
— Укажите, куда будут приходить ответы гостей:
• Telegram
• Email
• Google Таблица

💬 Ждём вашу информацию, и сразу приступим к созданию демо! 🙂
EOT;

// Обработка номера телефона для WhatsApp
$cleanPhone = preg_replace('/\D+/', '', $phone);

if (strlen($cleanPhone) >= 10) {
    if (strpos($cleanPhone, '8') === 0) {
        $cleanPhone = '7' . substr($cleanPhone, 1);
    }
    $whatsappUrl = "https://wa.me/$cleanPhone?text=" . rawurlencode($whatsappMessage);
} else {
    $cleanPhone = '';
    $whatsappUrl = '';
}

// Отправка письма клиенту
$subject = "Ваш заказ на сайте Welcome-to-day.ru"; 
$headers = "From: $siteName <$fromEmail>\r\n";
$headers .= "Reply-To: $fromEmail\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

$emailMessage = <<<EOM
Здравствуйте, $fullname!

Спасибо за ваш заказ на сайте welcome-to-day.ru 🎉

Ваш шаблон: $productName
Предварительная стоимость: $price ₽

Мы свяжемся с вами в ближайшее время, чтобы обсудить детали и подготовить демо-версию сайта.

Если хотите быстрее — напишите нам в WhatsApp:
$whatsappUrl

Хорошего дня!
EOM;

$emailSent = mail($email, $subject, $emailMessage, $headers);

if (!$emailSent) {
    $errors[] = "Не удалось отправить письмо на $email.";
    $success = false;
}

// Функции очистки и экранирования
function cleanText($text) {
    $text = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
    $text = str_replace("\xC2\xA0", ' ', $text);
    return trim($text);
}

function htmlEscape($s) {
    return htmlspecialchars(cleanText((string)$s), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if (is_numeric($price)) {
    $priceDisplay = htmlEscape($price . ' руб');
} else {
    $priceDisplay = htmlEscape($price);
}

// Формируем короткое сообщение для Telegram
$telegramMessage = "💌 Новый заказ Welcome-to-day\n";
$telegramMessage .= "Шаблон: " . htmlEscape($productName) . "\n";
$telegramMessage .= "Имя: " . htmlEscape($fullname) . "\n";
$telegramMessage .= "Телефон: " . htmlEscape($phone) . "\n";
$telegramMessage .= "Email: " . htmlEscape($email) . "\n";
if ($ad !== '') {
    $telegramMessage .= "Промокод: " . htmlEscape($ad) . "\n";
}
$telegramMessage .= "Цена: {$priceDisplay}\n";
if (!empty($whatsappUrl)) {
    // В Telegram используем HTML ссылку
    $escapedUrl = htmlspecialchars($whatsappUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $telegramMessage .= "WhatsApp: <a href=\"$escapedUrl\">Написать</a>\n";
}

// Логирование длины сообщения
file_put_contents('telegram_length.log', date('c') . " Length: " . mb_strlen($telegramMessage) . " chars\n", FILE_APPEND);

// Отправка в Telegram
function sendTelegramMessage($token, $chatId, $message) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $postFields = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['ok' => false, 'description' => "Curl error: $curlError"];
    }

    return json_decode($response, true);
}

file_put_contents('telegram_debug.log', date('c') . " MESSAGE: " . $telegramMessage . PHP_EOL, FILE_APPEND);

$telegramDecoded = sendTelegramMessage($adminTelegramToken, $adminChatId, $telegramMessage);

file_put_contents('telegram_api_response.log', date('c') . " RESPONSE: " . json_encode($telegramDecoded) . PHP_EOL, FILE_APPEND);

if (!$telegramDecoded['ok']) {
    $success = false;
    $errDesc = $telegramDecoded['description'] ?? 'Неизвестная ошибка Telegram';
    $errors[] = "Ошибка telegram: {$errDesc}";
    file_put_contents('telegram_error.log', date('c') . " ERROR: " . $errDesc . PHP_EOL, FILE_APPEND);
}

// Запоминаем время отправки
$_SESSION['last_order_time'] = time();

// Ответ фронту
echo json_encode([ 
    'success' => $success, 
    'message' => $success ? 'Спасибо! Ваш заказ принят.' : implode(' ', $errors), 
    'whatsapp' => $whatsappUrl 
]); 
exit;
?>

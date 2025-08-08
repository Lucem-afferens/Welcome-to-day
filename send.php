<?php 
ini_set('display_errors', 0); 
ini_set('display_startup_errors', 0); 
error_reporting(0); 
header('Content-Type: application/json'); 

session_start();

// === Антиспам: минимальный таймаут между отправками (например, 30 секунд) ===
$spamTimeout = 30; // в секундах
if (isset($_SESSION['last_order_time']) && (time() - $_SESSION['last_order_time']) < $spamTimeout) {
    echo json_encode([
        'success' => false,
        'message' => 'Пожалуйста, подождите немного перед повторной отправкой заказа.'
    ]);
    exit;
}


// === Получение данных из формы === 
$fullname = trim($_POST['fullname'] ?? ''); 
$phone = trim($_POST['phone'] ?? ''); 
$email = trim($_POST['email'] ?? ''); 
$ad = trim($_POST['ad'] ?? ''); 
$productName = trim($_POST['productName'] ?? ''); 
$firstPrice = trim($_POST['firstPrice'] ?? ''); 

// === Промокод и финальная цена === 
$discountPrice = 990; 
$discountCode = "katyalegenda"; 
$price = number_format((strtolower($ad) === $discountCode) ? $discountPrice : floatval($firstPrice));
$success = true; 
$errors = []; 

// === Настройки === 
$adminTelegramToken = '8469386738:AAEZqVpy0g-TVR8YFhJtZT8z3jWDVlNe3Ws'; 
$adminChatId = '7293309046'; 
$fromEmail = 'noreply@welcome-to-day.ru'; 
$siteName = 'Welcome-to-day'; 

// === Функция очистки текста для Telegram MarkdownV2 (экранирование спецсимволов) ===
function telegramMarkdownEscape($text) {
    $specialChars = ['_', '*', '[', ']', '(', ')', '~', '', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($specialChars as $char) {
        $text = str_replace($char, "\\".$char, $text);
    }
    return $text;
}

// === Формируем сообщение для клиента в WhatsApp === 
$whatsappMessage = <<<EOT
Здравствуйте! Спасибо за заказ на сайте welcome-to-day.ru 🎉

Ваш шаблон: «{$productName}»
Стоимость: $price ₽
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

// === Обработка номера телефона ===
$cleanPhone = preg_replace('/\D+/', '', $phone); // удалим всё кроме цифр

if (strlen($cleanPhone) >= 10) {
    // Заменим первую 8 на 7 для РФ
    if (strpos($cleanPhone, '8') === 0) {
        $cleanPhone = '7' . substr($cleanPhone, 1);
    }

    $whatsappUrl = "https://wa.me/$cleanPhone?text=" . rawurlencode($whatsappMessage);
} else {
    $cleanPhone = ''; // для Telegram
    $whatsappUrl = '';
}


$encodedWhatsappMessage = urlencode($whatsappMessage);
$whatsappMe = "https://wa.me/79226447689";

// === Отправка письма клиенту === 
$subject = "Ваш заказ на сайте Welcome-to-day.ru"; 
$headers = "From: $siteName <$fromEmail>\r\n";
$headers .= "Reply-To: $fromEmail\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";


$emailMessage = <<<EOM
Здравствуйте, $fullname!

Спасибо за ваш заказ на сайте welcome-to-day.ru 🎉

Ваш шаблон: $productName
Стоимость: $price ₽

Мы свяжемся с вами в ближайшее время, чтобы обсудить детали и подготовить демо-версию сайта.

Если хотите быстрее — напишите нам в WhatsApp:
$whatsappMe

Хорошего дня!
EOM;

$emailSent = mail($email, $subject, $emailMessage, $headers);
if (!$emailSent) {
    $errors[] = "Не удалось отправить письмо на $email.";
    $success = false;
}

function escapeMarkdownV2Link($url) {
    // Экранируем всё, что может сломать MarkdownV2
    $url = str_replace(
        ['(', ')', '[', ']', '\\', '.', '-', '_', '=', '&', '?'],
        ['\\(', '\\)', '\\[', '\\]', '\\\\', '\\.', '\\-', '\\_', '\\=', '\\&', '\\?'],
        $url
    );
    return $url;
}

// === Отправка уведомления админу в Telegram === 
$telegramMessage = "💌 *" . telegramMarkdownEscape("Новый заказ Welcome-to-day") . "*\n";
$telegramMessage .= "*Имя:* " . telegramMarkdownEscape($fullname) . "\n"; 
$telegramMessage .= "*Телефон:* " . telegramMarkdownEscape($phone) . "\n"; 
$telegramMessage .= "*Email:* " . telegramMarkdownEscape($email) . "\n"; 
$telegramMessage .= "*Шаблон:* " . telegramMarkdownEscape($productName) . "\n"; 
if ($ad !== '') {
    $telegramMessage .= "*Промокод:* " . telegramMarkdownEscape($ad) . "\n"; 
}
$telegramMessage .= "*Цена:* " . telegramMarkdownEscape($price . ' руб') . "\n"; 


if ($whatsappUrl) {
    $safeWhatsappUrl = escapeMarkdownV2Link($whatsappUrl);
    $telegramMessage .= telegramMarkdownEscape("Ссылка на WhatsApp: ") . "[$cleanPhone]($safeWhatsappUrl)\n";
} else {
    $telegramMessage .= telegramMarkdownEscape("Не указан корректный номер для WhatsApp\n");
}

$telegramMessage .= telegramMarkdownEscape("\n_Автоуведомление с сайта_");

$telegramData = [ 
    'chat_id' => $adminChatId, 
    'text' => $telegramMessage, 
    'parse_mode' => 'MarkdownV2'  // Используем MarkdownV2 с экранированием
]; 

$context = stream_context_create([ 
    'http' => [ 
        'header' => "Content-type: application/x-www-form-urlencoded", 
        'method' => 'POST', 
        'content' => http_build_query($telegramData) 
    ] 
]); 

file_put_contents('telegram_debug.log', print_r($telegramData, true));
$telegramResponse = file_get_contents("https://api.telegram.org/bot$adminTelegramToken/sendMessage", false, $context);

if ($telegramResponse === false) {
    $error = error_get_last();
    $errors[] = "Ошибка отправки в Telegram: " . $error['message'];
    $success = false;
} else {
    $telegramDecoded = json_decode($telegramResponse, true);
    if (!$telegramDecoded['ok']) {
        $errors[] = "Ошибка Telegram: " . $telegramDecoded['description'];
        $success = false;
    }
}

// === Сохраняем время отправки для антиспама ===
$_SESSION['last_order_time'] = time();

// === Ответ для фронта === 
echo json_encode([ 
    'success' => $success, 
    'message' => $success ? 'Спасибо! Ваш заказ принят.' : implode(' ', $errors), 
    'whatsapp' => $whatsappUrl 
]); 
exit;
?>
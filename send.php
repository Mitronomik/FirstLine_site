<?php
declare(strict_types=1);

$to = "ВАША_ПОЧТА@yandex.ru";
$from = "no-reply@ВАШ-ДОМЕН.ru";

function render_response(string $title, string $message, bool $success): void
{
    $accent = $success ? '#2f8f46' : '#b34a35';
    http_response_code($success ? 200 : 400);
    echo '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' — FIRST LINE</title><style>body{margin:0;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;background:#f9f7f4;color:#1a1714;line-height:1.6}.wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}.card{max-width:560px;background:#f2ede8;border:1px solid rgba(0,0,0,.1);border-radius:16px;padding:40px;box-shadow:0 12px 40px rgba(0,0,0,.1)}h1{margin:0 0 16px;font-family:Georgia,"Times New Roman",serif;font-size:clamp(2rem,5vw,3rem);line-height:1.1}.status{color:' . $accent . ';font-weight:700;text-transform:uppercase;letter-spacing:.08em;font-size:.8rem;margin-bottom:12px}.btn{display:inline-block;margin-top:24px;background:#1a1714;color:#fff;text-decoration:none;border-radius:999px;padding:12px 24px;font-weight:700}</style></head><body><main class="wrap"><section class="card"><div class="status">FIRST LINE</div><h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p><a class="btn" href="index.html#contact">Вернуться на сайт</a></section></main></body></html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    render_response('Форма принимает только POST-запросы', 'Пожалуйста, отправьте заявку через форму на сайте.', false);
}

$honeypot = trim((string)($_POST['website'] ?? ''));
if ($honeypot !== '') {
    render_response('Заявка принята', 'Спасибо! Если данные корректны, мы свяжемся с вами в течение рабочего дня.', true);
}

$phone = trim((string)($_POST['phone'] ?? ''));
$digits = preg_replace('/\D+/', '', $phone) ?? '';

if ($phone === '' || strlen($digits) < 10 || strlen($digits) > 15 || !preg_match('/^[0-9+()\s\-]{7,25}$/u', $phone)) {
    render_response('Проверьте номер телефона', 'Укажите корректный номер телефона и отправьте заявку ещё раз.', false);
}

$subject = 'Заявка FIRST LINE';
$body = "Новая заявка с сайта FIRST LINE\n\nТелефон: {$phone}\nДата: " . date('d.m.Y H:i:s') . "\n";
$headers = [
    'From: FIRST LINE <' . $from . '>',
    'Reply-To: ' . $from,
    'Content-Type: text/plain; charset=UTF-8',
    'MIME-Version: 1.0',
    'X-Mailer: PHP/' . phpversion(),
];

$sent = mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));

if ($sent) {
    render_response('Заявка отправлена', 'Спасибо! Мы получили ваш номер и свяжемся с вами в течение рабочего дня.', true);
}

render_response('Не удалось отправить заявку', 'Письмо не отправилось. Пожалуйста, попробуйте ещё раз позже или свяжитесь с нами другим удобным способом.', false);

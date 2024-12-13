<?php
// Укажите ваш секрет для проверки подписи (установите его в вебхуке на GitHub)
$secret = 'ваш_секретный_ключ';

// Получение содержимого запроса
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Проверка подписи (если настроен секрет)
if (isset($headers['X-Hub-Signature-256'])) {
    $signature = hash_hmac('sha256', $payload, $secret);
    if ('sha256=' . $signature !== $headers['X-Hub-Signature-256']) {
        http_response_code(403);
        exit('Invalid signature');
    }
}

// Декодируем входящий JSON
$data = json_decode($payload, true);

// Проверяем событие "push"
if (isset($data['ref']) && $data['ref'] === 'refs/heads/main') {
    // Путь к вашему репозиторию на сервере
    $repoPath = '/path/to/repository'; // Укажите путь к клонированному репозиторию

    // Выполняем команду `git pull`
    exec("cd $repoPath && git pull origin main", $output, $return_var);

    if ($return_var === 0) {
        echo 'Обновление успешно выполнено:';
        echo implode("\n", $output);
    } else {
        echo 'Ошибка при обновлении:';
        echo implode("\n", $output);
    }
} else {
    echo 'Это не событие push на ветку main.';
}
?>
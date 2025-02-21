<?php
session_start();

// Ваш client_id и secret_key
$client_id = '52605801';
$secret_key = 'xH2LeQx7wUqH4GKttfdD';

// URL обратного вызова
$callback_url = 'https://example.com/oauth_callback.php';

// Проверка наличия access_token в сессии
if (!isset($_SESSION['access_token'])) {
    // Генерация случайной строки для state
    $_SESSION['state'] = bin2hex(random_bytes(32));

    // Формирование URL для авторизации
    $auth_url = 'https://oauth.vk.com/authorize?' . http_build_query([
        'client_id'     => $client_id,
        'redirect_uri'  => $callback_url,
        'response_type' => 'code',
        'scope'         => 'groups,wall,offline',
        'state'         => $_SESSION['state'],
        'v'             => '5.131',
    ]);

    header("Location: $auth_url");
    exit;
}

// Если есть access_token, продолжаем работу с VK API
$access_token = $_SESSION['access_token'];

// Пример запроса к VK API для получения информации о пользователе






$vk_session = new VkApi($access_token);
$vk = $vk_session->getApi();
$group_id = 123456;
$posts_count = get_posts_count($vk, $group_id);
echo "Total posts: " . $posts_count;





function get_posts_count($vk, $group_id) {
    $response = $vk->wall()->get(['owner_id' => -$group_id, 'count' => 1]);
    return $response['count'];
    $total=$response['count']
}



// Теперь можно продолжить работу с книгой постов...
//script for page flipping
$book = new BookPagination($total);

// Navigate through pages
$book->nextPage();     // Returns 2
$book->nextPage();     // Returns 3
$book->previousPage(); // Returns 2
$book->goToPage(50);   // Returns 50

// Get current position
echo $book->getCurrentPage(); // Outputs 50
echo $book->getTotalPages(); // Outputs 100

Copy

Apply
?>
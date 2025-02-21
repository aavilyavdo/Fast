<?function getVkPosts($groupId, $accessToken) {
    $url = "https://api.vk.com/method/wall.get?owner_id=-{}&count=100&access_token={$accessToken}&v=5.131";
    $response = file_get_contents($url);
    return json_decode($response, true);
}
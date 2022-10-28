<?php

function getFormData($method) {
    if ($method === 'GET') return $_GET;
    if ($method === 'POST' && $_POST) return $_POST;

    $json = json_decode(file_get_contents('php://input'));
    if ($json) return (array) $json;

    $data = array();
    $exploded = explode('&', file_get_contents('php://input'));
    foreach ($exploded as $pair) {
        $item = explode('=', $pair);
        if (count($item) == 2) {
            $data[urldecode($item[0])] = urldecode($item[1]);
        }
    }
    return $data;
}

$method = $_SERVER['REQUEST_METHOD'];

$formData = getFormData($method);

$url = (isset($_GET['q'])) ? $_GET['q'] : '';
$url = rtrim($url, '/');
$urls = explode('/', $url);

$router = $urls[0];
$urlData = array_slice($urls, 1);
 if ($router == "stories") {
    require_once 'function.php';
    route($method, $urlData, $formData); 
 } else if ($router == "movies") {
    require_once 'movies.php';
    route($method, $urlData, $formData); 
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(array(
        'error' => 'Bad Request'
    ));
 }

?>
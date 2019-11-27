<?php
$url = $_SERVER['REQUEST_URI'];
$path =parse_url($url)['path'];
$method = $_SERVER['REQUEST_METHOD'];
var_dump($path);

$routes = [
    'GET' =>[
        '/shoutit/' =>'homeHandler'
    ],
    'POST' =>[
        '/shoutit/create-shoutit' => 'createShoutit'
    ]

];

$handler = $routes[$method][$path]??'';
var_dump($handler);
if($handler && is_callable($handler)){
    $conn = new mysqli('localhost', 'root', '', 'shoutit', 3306);
    $conn->set_charset('utf8');
    $handler($conn, $_GET,$_POST);

}else{
    echo '404';
}

function homeHandler(mysqli $conn,$query,$body)
{
    $result = $conn -> query("SELECT * FROM shouts");
    $shouts = [];
    while ($data = $result->fetch_assoc()) {
        $shouts[] = $data;
    }
    require 'home.phtml';
}

function createShoutit(mysqli $conn,$query,$body)
{
    var_dump('csaa');
}

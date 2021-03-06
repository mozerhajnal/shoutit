<?php

$routes = [
    'GET' =>[
        '/' =>'homeHandler'
    ],
    'POST' =>[
        '/create-shout' => 'createShoutit'
    ]

];

$url = $_SERVER['REQUEST_URI'];
$path =parse_url($url)['path'];
$method = $_SERVER['REQUEST_METHOD'];


$handler = $routes[$method][$path]??'';
if($handler && is_callable($handler)){
    //Get Heroku ClearDB connection information
$cleardb_url      = parse_url(getenv("CLEARDB_DATABASE_URL"));
$cleardb_server   = $cleardb_url["host"];
$cleardb_username = $cleardb_url["user"];
$cleardb_password = $cleardb_url["pass"];
$cleardb_db       = substr($cleardb_url["path"],1);

    $conn = new mysqli($cleardb_server, $cleardb_username, $cleardb_password, $cleardb_db);
    $conn->set_charset('utf8');
    $handler($conn, $_GET,$_POST);

}else
{
    echo '404';
}

function homeHandler(mysqli $conn,$query,$body)
{
    $result = $conn -> query("SELECT * FROM shouts ORDER BY id DESC");
    $shouts = [];
    while ($data = $result->fetch_assoc()) {
        $shouts[] = $data;
    }
    require 'home.phtml';
}

function createShoutit(mysqli $conn,$query,$body)
{
    $user = $body['user'];
    $message = $body['message'];
    date_default_timezone_set('Europe/Budapest');
    $time = date('H:i:s a',time());

    if(!isset($user) || $user == '' || !isset($message) || $message == ''){
        $error = "Please fill in your name and a message";
        header("Location: /?error=".urlencode($error));
        return;
    }

    $queryString = "INSERT INTO shouts (user,message, time) VALUES(?,?,?)";

    $statement = $conn -> prepare($queryString);
    $statement -> bind_param('sss',$user,$message,$time);
    $isSuccess = $statement -> execute();

    if(!$isSuccess){
        header("Location: /?error=".urlencode($error));
        return;
    }

    header("Location: /");
}

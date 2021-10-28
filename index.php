<?php

$env = getenv("PHPSERVER");
if(!$env){
    $env = "prod";
}
//echo $_SERVER['HTTP_HOST']."---";
//echo $env. " -- ";
if($_SERVER['HTTP_HOST'] == '54.254.71.237:9000' || $_SERVER["HTTP_HOST"] == "localhost:8000")
{
    $env = "dev";

    //echo $env. " -- ";
}

define("_ENV", $env);

if($env === "dev")
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    ini_set("upload_max_filesize", "100M");
    ini_set("post_max_size", "150M");
}

if($env !== "dev")
{
    $isHTTPS = $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https" ||
        $_SERVER["HTTP_X_FORWARDED_PORT"] == "443" ||
        $_SERVER["PORT"] == "443" ||
        $_SERVER["SERVER_PORT"] == "443" ||
        $_SERVER["HTTPS"] == "on";

    //Force HTTPS
    if($isHTTPS == false && $_SERVER["HTTP_HOST"] != "localhost:8000")
    {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}

//die(_ENV);
session_start();
require_once __DIR__ . '/app/Config.php';
require_once ROOT_DIR . '/vendor/autoload.php';
require_once ROOT_DIR . '/app/App.php';
require_once ROOT_DIR . '/functions.php';


$app = new App();
$app->init();
$app->process();
$app->render();
$app->terminate();

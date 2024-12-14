<?php

require_once("mysql_connect.php");
require_once("smarty/libs/Smarty.class.php");

require_once("php/Controllers/ContentController.php");

session_start();

if (!isset($_SESSION['previous_pages']))
{
    $_SESSION['previous_pages'] = [];
}

$smarty  = new Smarty\Smarty();

if(isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = "home";
}

//we fetch the content page according to the requested page
$content = new ContentController($bdd, $smarty, $page);
$content_page = $content->GetContent();
$content->SetPreviousButton($page);

$smarty->assign("Content", $content_page);

header('Content-Type: text/html; charset=utf-8');
$smarty->display("html/index.html");

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

$pagesCount = count($_SESSION['previous_pages']);
$pageUrl = "";

// We check if we come from the previous button to update the 'previous_pages' session array
if (isset($_GET['previousPage']))
{
    if ($pagesCount > 1)
    {
        array_splice($_SESSION['previous_pages'], $pagesCount - 1, 1);
        $pagesCount = count($_SESSION['previous_pages']);
    }
}

// We set previous page variable that will be used the view
if ($pagesCount > 0)
{
    if (isset($_GET['previousPage']))
    {
        if ($pagesCount > 1)
        {
            $pageUrl = $_SESSION['previous_pages'][$pagesCount - 2];
        }
    }
    else
    {
        $pageUrl = $_SESSION['previous_pages'][$pagesCount - 1];
    }
}

// If we don't come from the previous button, we update the 'previous_pages' array
if (!isset($_GET['previousPage']))
{
    $newPreviousPageUrl = $content->GetPreviousPageUrl($page, $_GET);

    if ($pagesCount == 0 || ($pagesCount > 0 && strcmp($_SESSION['previous_pages'][$pagesCount - 1], $newPreviousPageUrl) != 0)) 
    {
        array_push($_SESSION['previous_pages'], $newPreviousPageUrl);
        $pagesCount++;
    }
}

$smarty->assign("Content", $content_page);
$smarty->assign("PreviousPage", $pageUrl);

header('Content-Type: text/html; charset=utf-8');
$smarty->display("html/index.html");

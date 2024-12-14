<?php

require_once("php/Controllers/MealController.php");
require_once("php/Controllers/IngredientController.php");
require_once("php/Controllers/WeekController.php");

class ContentController {

    private $bdd;
    private $smarty;
    private $page;

    public function __construct($bdd, $smarty, $page) {
        $this->bdd      = $bdd;
        $this->smarty   = $smarty;
        $this->page     = $page;
    }

    public function GetContent() {
        $content = "";

        switch($this->page) {
            case MealController::GetRoute():
                return $this->GetMeals();
                break;
            case IngredientController::GetRoute():
                return $this->GetIngredients();
                break;
            case WeekController::GetRoute():
                return $this->GetWeeks();
                break;
            default:
                return $this->GetWeeks();
        }

        return $content;
    }

    public function GetPreviousPageUrl($page, $get)
    {
        $url = "index.php?page=".$page."&previousPage=1";
        foreach ($get as $key => $value)
        {
            $url .= "&".$key."=".$value;
        }
        return $url;
    }

    private function GetMeals()
    {
        $meals = new MealController($this->bdd, $this->smarty);
        return $meals->GetContent();
    }

    private function GetIngredients()
    {
        $ingredients = new IngredientController($this->bdd, $this->smarty);
        return $ingredients->GetContent();
    }

    private function GetWeeks()
    {
        $weeks = new WeekController($this->bdd, $this->smarty);
        return $weeks->GetContent();
    }

    public function SetPreviousButton($page)
    {
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
            $newPreviousPageUrl = $this->GetPreviousPageUrl($page, $_GET);

            if ($pagesCount == 0 || ($pagesCount > 0 && strcmp($_SESSION['previous_pages'][$pagesCount - 1], $newPreviousPageUrl) != 0)) 
            {
                array_push($_SESSION['previous_pages'], $newPreviousPageUrl);
                $pagesCount++;
            }
        }

        $this->smarty->assign("PreviousPage", $pageUrl);
    }
}

?>
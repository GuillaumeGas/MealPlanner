<?php

require_once("php/Controllers/MealController.php");
require_once("php/Controllers/IngredientController.php");
require_once("php/Controllers/WeekController.php");
//require_once("php/MealFinder.class.php");


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
}

?>
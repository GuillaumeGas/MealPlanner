<?php

require_once("php/Controllers/BaseController.php");
require_once("php/Models/MealModel.php");
require_once("php/Models/IngredientModel.php");

class MealController extends BaseController
{
    const MEAL_ROUTE = "meals";

    private $mealModel;
    private $ingredientModel;

    public function __construct($bdd, $smarty) {
        $actions = array(
            "add" => array($this, "AddMeal"),
            "delete" => array($this, "DeleteMeal")
        );

        $this->mealModel = new MealModel($bdd);
        $this->ingredientModel = new IngredientModel($bdd);

        parent::__construct($bdd, $smarty, $actions, self::MEAL_ROUTE);
    }

    public function GetContent() {
        if(isset($_GET['action'])) {
            return $this->ExecuteAction($_GET['action']);
        } else if (isset($_GET['id'])) {
            return $this->PrintMeal($_GET['id']);
        } else {
            return $this->PrintMeals();
        }
    }

    public static function GetRoute()
    {
        return self::MEAL_ROUTE;
    }

    private function PrintMeals()
    {
        $this->smarty->assign("Meals", $this->mealModel->GetAll());
        return $this->smarty->fetch("html/meals.html");
    }

    public function AddMeal()
    {
        if (isset($_POST["mealName"]))
        {
            $idIngredients = (isset($_POST['idIngredients']) ? $_POST['idIngredients'] : array());
            $this->mealModel->Add($_POST['mealName'], isset($_POST["quickToMake"]), $idIngredients);
            return $this->PrintMeals();
        } else {
            $ingredients = $this->ingredientModel->GetAll();
            $this->smarty->assign("Ingredients", $ingredients);
            return $this->smarty->fetch("html/addMeal.html");
        }
    }

    private function PrintMeal($id)
    {
        $this->smarty->assign("Meal", $this->mealModel->GetFromId($id));
        return $this->smarty->fetch("html/meal.html");
    }

    public function DeleteMeal()
    {
        if (isset($_GET['id']))
        {
            $this->mealModel->Delete($_GET['id']);
        }

        return $this->PrintMeals();
    }
}
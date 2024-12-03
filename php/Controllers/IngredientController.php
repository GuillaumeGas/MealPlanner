<?php

require_once("php/Controllers/BaseController.php");
require_once("php/Models/IngredientModel.php");

class IngredientController extends BaseController
{
    private $ingredientModel;

    const INGREDIENT_ROUTE = "ingredients";
    const ACTION_ADD_INGREDIENT = 1;
    const ACTION_SET_INGREDIENT = 2;

    public function __construct($bdd, $smarty) {
        $actions = array(
            "add" => array($this, "AddIngredient"),
            "set" => array($this, "SetIngredient"),
            "delete" => array($this, "DeleteIngredient")
        );

        $this->ingredientModel = new IngredientModel($bdd);

        parent::__construct($bdd, $smarty, $actions, self::INGREDIENT_ROUTE);
    }

    public function GetContent() {
        if(isset($_GET['action'])) {
            return $this->ExecuteAction($_GET['action']);
        } else {
            return $this->PrintIngredients();
        }
    }

    public static function GetRoute()
    {
        return self::INGREDIENT_ROUTE;
    }

    private function PrintIngredients()
    {
        $this->smarty->assign("Ingredients", $this->ingredientModel->GetAll());
        return $this->smarty->fetch("html/ingredients.html");
    }

    public function AddIngredient()
    {
        if (isset($_POST["ingredientName"]) && isset($_POST["type"]) && isset($_POST["unit"]))
        {
            $this->ingredientModel->Add($_POST["ingredientName"], $_POST["type"], $_POST["unit"]);
            return $this->PrintIngredients();
        }

        $this->smarty->assign("Action", self::ACTION_ADD_INGREDIENT);

        return $this->smarty->fetch("html/addIngredient.html");
    }

    public function DeleteIngredient()
    {
        if (isset($_GET['id']))
        {
            $this->ingredientModel->Delete($_GET['id']);
        }

        return $this->PrintIngredients();
    }

    public function SetIngredient()
    {
        if (!isset($_GET['id']) && !isset($_POST["ingredientId"]))
        {
            return $this->PrintIngredients();
        }

        $ingredientId = isset($_GET['id']) ? $_GET['id'] : $_POST["ingredientId"];

        if (isset($_POST["ingredientName"]) && isset($_POST["type"]) && isset($_POST["unit"]))
        {
            $this->ingredientModel->Set($ingredientId, $_POST["ingredientName"], $_POST["type"], $_POST["unit"]);
        }

        $ingredient = $this->ingredientModel->GetFromId($ingredientId);
        $this->smarty->assign("Ingredient", $ingredient);
        $this->smarty->assign("Action", self::ACTION_SET_INGREDIENT);
        return $this->smarty->fetch("html/addIngredient.html");
    }
}
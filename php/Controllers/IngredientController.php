<?php

require_once("php/Controllers/BaseController.php");
require_once("php/Models/IngredientModel.php");

class IngredientController extends BaseController
{
    private $ingredientModel;

    const INGREDIENT_ROUTE = "ingredients";

    public function __construct($bdd, $smarty) {
        $actions = array(
            "add" => array($this, "AddIngredient"),
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
}
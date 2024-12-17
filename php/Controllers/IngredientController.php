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

            $this->userMessagesHandler->AddMessage(0, "'".$_POST["ingredientName"]."' ajouté avec succès !");

            return $this->PrintIngredients();
        }

        $this->smarty->assign("Action", self::ACTION_ADD_INGREDIENT);

        return $this->smarty->fetch("html/addIngredient.html");
    }

    public function DeleteIngredient()
    {
        if (isset($_GET['id']))
        {
            $ingredient = $this->ingredientModel->GetFromId($_GET['id']);
            if (!$ingredient)
            {
                $this->userMessagesHandler->AddMessage(2, "Ingredient introuvable");
            }
            else
            {
                if ($this->ingredientModel->Delete($_GET['id']))
                {
                    $this->userMessagesHandler->AddMessage(0, "'".$ingredient['Name']."' supprimé avec succès !");
                }
                else
                {
                    $this->userMessagesHandler->AddMessage(2, "Une erreur est survenue lors de la suppression de l'ingredient '".$ingredient['Name']."'.");
                }
            }
        }
        else
        {
            $this->userMessagesHandler->AddMessage(2, "Ingredient introuvable");
        }

        return $this->PrintIngredients();
    }

    public function SetIngredient()
    {
        if (!isset($_GET['id']) && !isset($_POST["ingredientId"]))
        {
            $this->userMessagesHandler->AddMessage(2, "Une erreur est survenue lors de l'ajout ou la modification d'un ingrédient.");
            return $this->PrintIngredients();
        }

        $ingredientId = isset($_GET['id']) ? $_GET['id'] : $_POST["ingredientId"];

        if (isset($_POST["ingredientName"]) && isset($_POST["type"]) && isset($_POST["unit"]))
        {
            if ($this->ingredientModel->Set($ingredientId, $_POST["ingredientName"], $_POST["type"], $_POST["unit"]))
            {
                $this->userMessagesHandler->AddMessage(0, "Ingredient '".$_POST["ingredientName"]."' modifié avec succès !");
            }
            else
            {
                $this->userMessagesHandler->AddMessage(2, "Une erreur est survenue lors de la modification de l'ingrédient '".$_POST['ingredientName']."'.");
                return $this->PrintIngredients();
            }
        }

        $ingredient = $this->ingredientModel->GetFromId($ingredientId);

        if (!$ingredient)
        {
            $this->userMessagesHandler->AddMessage(2, "Une erreur est survenue lors de la modification, ingrédient introuvable.");
            return $this->PrintIngredients();
        }

        $this->smarty->assign("Ingredient", $ingredient);
        $this->smarty->assign("Action", self::ACTION_SET_INGREDIENT);
        return $this->smarty->fetch("html/addIngredient.html");
    }
}
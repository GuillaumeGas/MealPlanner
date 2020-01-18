<?php

require_once ("php/Models/IngredientModel.php");

class MealModel
{
    private $bdd;
    private $ingredientModel;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;

        $this->ingredientModel = new IngredientModel($bdd);
    }

    public function GetAll()
    {
        $query = $this->bdd->prepare("SELECT * FROM Meal");
        $query->execute();

        $index = 0;
        $meals = array();
        while ($data = $query->fetch())
        {
            $meals[$index] = $data;

            $ingredients = $this->GetIngredientsOfMeal($data['Id']);
            $meals[$index]["Ingredients"] = $ingredients;

            if ($data["Type"] == Meat)
                $meals[$index]["TypeStr"] = "Viande";
            else if ($data["Type"] == Fish)
                $meals[$index]["TypeStr"] = "Poisson";
            else
                $meals[$index]["TypeStr"] = "Veg";

            if ($data["QuickToMake"] == 0)
                $meals[$index]["QuickToMakeStr"] = "Non";
            else
                $meals[$index]["QuickToMakeStr"] = "Oui";

            $index++;
        }

        return $meals;
    }

    public function Add($name, $quickToMake, array $ingredientsId)
    {
        $type = 0;
        $ingredients = array();
        foreach ($ingredientsId as $ingId) {
            $ing = $this->ingredientModel->GetFromId($ingId);
            if ($ing["Type"] == Meat || $ing["Type"] == Fish)
                $type = $ing["Type"];

            $ingredients[] = $ing;
        }

        $query = $this->bdd->prepare("INSERT INTO meal VALUES(NULL, :name, :type, :quickToMake)");
        $query->execute(array(":name" => $name, ":type" => $type, ":quickToMake" => $quickToMake));

        $meal = $this->GetFromName($name);

        foreach($ingredients as $ing) {
            $query = $this->bdd->prepare("INSERT INTO ingredientsofmeal VALUES(NULL, :ingredientId, :mealId)");
            $query->execute(array(":mealId" => $meal["Id"], ":ingredientId" => $ing["Id"]));
        }

        return $meal;
    }

    public function GetFromName($name)
    {
        $query = $this->bdd->prepare("SELECT * FROM meal WHERE Name = :name");
        $query->execute(array(":name" => $name));
        return $query->fetch();
    }

    public function GetFromId($id)
    {
        $query = $this->bdd->prepare("SELECT * FROM meal WHERE Id = :id");
        $query->execute(array(":id" => $id));

        $res = $query->fetch();
        $res["Ingredients"] = $this->GetIngredientsOfMeal($id);

        if ($res["Type"] == Meat)
            $res["TypeStr"] = "Viande";
        else if ($res["Type"] == Fish)
            $res["TypeStr"] = "Poisson";
        else
            $res["TypeStr"] = "Veg";

        if ($res["QuickToMake"] == 0)
            $res["QuickToMakeStr"] = "Non";
        else
            $res["QuickToMakeStr"] = "Oui";

        return $res;
    }

    public function Delete($id)
    {
        $query = $this->bdd->prepare("DELETE FROM ingredientsofmeal WHERE MealId = :id");
        $query->execute(array(":id" => $id));

        $query = $this->bdd->prepare("DELETE FROM meal WHERE Id = :id");
        $query->execute(array(":id" => $id));
    }

    public function GetIngredientsOfMeal($id)
    {
        $query = $this->bdd->prepare("SELECT * FROM Ingredient WHERE Id IN (SELECT IngredientId FROM ingredientsofmeal WHERE MealId = :id)");
        $query->execute(array(":id" => $id));

        $ingredients = array();
        $index = 0;
        while ($data = $query->fetch())
        {
            $ingredients[$index] = $data;

            if ($ingredients[$index]["Type"] == Meat)
                $ingredients[$index]["TypeStr"] = "Viande";
            else if ($ingredients[$index]["Type"] == Fish)
                $ingredients[$index]["TypeStr"] = "Poisson";
            else
                $ingredients[$index]["TypeStr"] = "Veg";

            $index++;
        }

        return $ingredients;
    }
}
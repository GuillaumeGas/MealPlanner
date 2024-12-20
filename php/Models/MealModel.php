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
                $meals[$index]["TypeStr"] = "Meat";
            else if ($data["Type"] == Chicken)
                $meals[$index]["TypeStr"] = "Chicken";
            else if ($data["Type"] == Fish)
                $meals[$index]["TypeStr"] = "Fish";
            else
                $meals[$index]["TypeStr"] = "Veg";

            if ($data["QuickToMake"] == 0)
                $meals[$index]["QuickToMakeStr"] = "No";
            else
                $meals[$index]["QuickToMakeStr"] = "Yes";

            $index++;
        }

        return $meals;
    }

    public function Add($name, $quickToMake, array $ingredientsId)
    {
        $type = -1;
        $ingredients = array();

        foreach ($ingredientsId as $ingId) {
            $ing = $this->ingredientModel->GetFromId($ingId);
            if ($ing["Type"] == Meat || $ing["Type"] == Fish || $ing["Type"] == Chicken)
                $type = $ing["Type"];

            $ingredients[] = $ing;
        }

        if ($type == -1)
            $type = Veg;

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
            $res["TypeStr"] = "Meat";
        else if ($res["Type"] == Chicken)
            $res["TypeStr"] = "Chicken";
        else if ($res["Type"] == Fish)
            $res["TypeStr"] = "Fish";
        else
            $res["TypeStr"] = "Veg";

        if ($res["QuickToMake"] == 0)
            $res["QuickToMakeStr"] = "No";
        else
            $res["QuickToMakeStr"] = "Yes";

        return $res;
    }

    public function Delete($id)
    {
        $query = $this->bdd->prepare("DELETE FROM ingredientsofmeal WHERE MealId = :id");
        return $query->execute(array(":id" => $id));

        $query = $this->bdd->prepare("DELETE FROM meal WHERE Id = :id");
        return $query->execute(array(":id" => $id));
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
                $ingredients[$index]["TypeStr"] = "Meat";
            else if ($ingredients[$index]["Type"] == Chicken)
                $ingredients[$index]["TypeStr"] = "Chicken";
            else if ($ingredients[$index]["Type"] == Fish)
                $ingredients[$index]["TypeStr"] = "Fish";
            else
                $ingredients[$index]["TypeStr"] = "Veg";

            $index++;
        }

        return $ingredients;
    }
}
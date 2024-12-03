<?php

define ("Meat", 0);
define ("Fish", 1);
define ("Chicken", 2);
define ("Veg", 3);

class IngredientModel
{
    private $bdd;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
    }

    public function GetAll()
    {
        $query = $this->bdd->prepare("SELECT * FROM Ingredient");
        $query->execute();

        $index = 0;
        $ingredients = array();
        while ($data = $query->fetch())
        {
            $ingredients[$index] = $data;
            if ($data["Type"] == Meat)
                $ingredients[$index]["TypeStr"] = "Viande";
            else if ($data["Type"] == Fish)
                $ingredients[$index]["TypeStr"] = "Poisson";
            else if ($data["Type"] == Chicken)
                $ingredients[$index]["TypeStr"] = "Poulet";
            else
                $ingredients[$index]["TypeStr"] = "Veg";

            $index++;
        }

        return $ingredients;
    }

    public function GetFromId($id)
    {
        $query = $this->bdd->prepare("SELECT * FROM Ingredient WHERE Id = :id");
        $query->execute(array(":id" => $id));
        $res =  $query->fetch();

        if ($res["Type"] == Meat)
            $res["TypeStr"] = "Viande";
        else if ($res["Type"] == Chicken)
            $res["TypeStr"] = "Poulet";
        else if ($res["Type"] == Fish)
            $res["TypeStr"] = "Poisson";
        else
            $res["TypeStr"] = "Veg";

        return $res;
    }

    public function Add($name, $type, $unit)
    {
        $query = $this->bdd->prepare("INSERT INTO ingredient VALUES(NULL, :name, :type, :unit)");
        $query->execute(array(":name" => $name, ":type" => $type, ":unit" => $unit));
    }

    public function Set($id, $name, $type, $unit)
    {
        $query = $this->bdd->prepare("UPDATE ingredient SET Name = :name, Type = :type, Unit = :unit WHERE Id = :id");
        $query->execute(array(":name" => $name, ":type" => $type, ":unit" => $unit, ":id" => $id));
    }

    public function Delete($id)
    {
        $query = $this->bdd->prepare("DELETE FROM ingredient WHERE Id = :id");
        $query->execute(array(":id" => $id));
    }
}
<?php

require_once ("php/Models/MealModel.php");

class WeekModel
{
    private $bdd;
    private $mealModel;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
        $this->mealModel = new MealModel($bdd);
    }

    public function GetAll()
    {
        $query = $this->bdd->prepare("SELECT * FROM Week");
        $query->execute();

        $index = 0;
        $weeks = array();
        while ($data = $query->fetch())
        {
            $weeks[$index] = $data;

            $meals = $this->GetMealsOfWeek($data['Id']);
            $weeks[$index]["Meals"] = $meals;

            $index++;
        }

        return $weeks;
    }

    public function Add(array $mealsId, $date)
    {
        $query = $this->bdd->prepare("INSERT INTO week VALUES(NULL, :date)");
        $query->execute(array(":date" => $date));

        $week = $this->GetLast();

        $index = 1;
        foreach($mealsId as $meal) {
            $query = $this->bdd->prepare("INSERT INTO mealsofweek VALUES(NULL, :weekId, :mealId, :dayNumber)");
            $query->execute(array(":weekId" => $week["Id"], ":mealId" => $meal, ":dayNumber" => $index++));
        }

        return $week;
    }

    public function GetFromDate($date)
    {
        $query = $this->bdd->prepare("SELECT * FROM week WHERE Date = :date");
        $query->execute(array(":date" => $date));
        return $query->fetch();
    }

    public function GetFromId($id)
    {
        $query = $this->bdd->prepare("SELECT * FROM week WHERE Id = :id");
        $query->execute(array(":id" => $id));

        $res = $query->fetch();
        $res["Meals"] = $this->GetMealsOfWeek($id);

        return $res;
    }

    public function Delete($id)
    {
        $query = $this->bdd->prepare("DELETE FROM mealsOfWeek WHERE WeekId = :id");
        $resQuery1 = $query->execute(array(":id" => $id));

        $query = $this->bdd->prepare("DELETE FROM week WHERE Id = :id");
        $resQuery2 = $query->execute(array(":id" => $id));

        return $resQuery1 && $resQuery2;
    }

    public function GetMealsOfWeek($id)
    {
        $query = $this->bdd->prepare("SELECT * FROM Meal LEFT JOIN mealsofweek ON Meal.Id = mealsofweek.MealId WHERE WeekId = :id ORDER BY Day ASC ");
        $query->execute(array(":id" => $id));

        $daysName = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");

        $meals = array();
        $index = 0;
        while ($data = $query->fetch())
        {
            $meals[$index] = $data;

            if ($data["Type"] == Meat)
                $meals[$index]["TypeStr"] = "Viande";
            else if ($data["Type"] == Chicken)
                $meals[$index]["TypeStr"] = "Poulet";
            else if ($data["Type"] == Fish)
                $meals[$index]["TypeStr"] = "Poisson";
            else
                $meals[$index]["TypeStr"] = "Veg";

            if ($data["QuickToMake"] == 0)
                $meals[$index]["QuickToMakeStr"] = "Non";
            else
                $meals[$index]["QuickToMakeStr"] = "Oui";

            $meals[$index]["DayName"] = $daysName[$index];

            $index++;
        }

        return $meals;
    }

    private function GetLast()
    {
        $query = $this->bdd->prepare("SELECT * FROM Week ORDER BY Id DESC LIMIT 1");
        $query->execute();
        return $query->fetch();
    }
}
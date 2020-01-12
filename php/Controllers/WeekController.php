<?php

require_once("php/Controllers/BaseController.php");
require_once("php/Models/WeekModel.php");
require_once("php/Models/MealModel.php");

class WeekController extends BaseController
{
    const WEEK_ROUTE = "weeks";

    private $weekModel;
    private $mealModel;

    public function __construct($bdd, $smarty) {
        $actions = array(
            "add" => array($this, "AddWeek"),
            "delete" => array($this, "DeleteWeek")
        );

        $this->weekModel = new WeekModel($bdd);
        $this->mealModel = new MealModel($bdd);

        parent::__construct($bdd, $smarty, $actions, self::WEEK_ROUTE);
    }

    public function GetContent() {
        if(isset($_GET['action'])) {
            return $this->ExecuteAction($_GET['action']);
        } else if (isset($_GET['id'])) {
            return $this->PrintWeek($_GET['id']);
        } else {
            return $this->PrintWeeks();
        }
    }

    public static function GetRoute()
    {
        return self::WEEK_ROUTE;
    }

    private function PrintWeeks()
    {
        $this->smarty->assign("Weeks", $this->weekModel->GetAll());
        return $this->smarty->fetch("html/weeks.html");
    }

    public function AddWeek()
    {
        if (isset($_POST["mealsId"]))
        {
            $date = date("Y-m-d");
            $this->weekModel->Add($_POST['mealsId'], $date);
            return $this->PrintWeeks();
        } else {
            $daysName = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
            $days = array();
            for($i = 0; $i < 7; $i++)
            {
                $meals = $this->mealModel->GetAll();
                $days[$i]["Meals"] = $meals;
                $days[$i]["DayNum"] = $i+1;
                $days[$i]["DayName"] = $daysName[$i];
            }

            $this->smarty->assign("Days", $days);
            return $this->smarty->fetch("html/addWeek.html");
        }
    }

    private function PrintWeek($id)
    {
        $this->smarty->assign("Week", $this->WeekModel->GetFromId($id));
        return $this->smarty->fetch("html/week.html");
    }

    public function DeleteWeek()
    {
        if (isset($_GET['id']))
        {
            $this->weekModel->Delete($_GET['id']);
        }

        return $this->PrintWeeks();
    }
}
<?php

require_once ("php/Controllers/MessageController.php");
require_once("php/Tools/UserMessagesHandler.php");

abstract class BaseController
{
    protected $bdd;
    protected $smarty;
    protected $actions;
    protected $defaultRoute;
    protected $userMessagesHandler;

    function __construct($bdd, $smarty, $actions, $defaultRoute)
    {
        $this->bdd    = $bdd;
        $this->smarty = $smarty;
        $this->actions = $actions;
        $this->defaultRoute = $defaultRoute;
        $this->userMessagesHandler = UserMessagesHandler::GetInstance();
    }

    protected function ExecuteAction($action)
    {
        if (!array_key_exists($action, $this->actions))
            return MessageController::msg("Error. Bad action.", $this->defaultRoute, $this->smarty);

        return call_user_func($this->actions[$action]);
    }

    abstract public function GetContent();
    abstract public static function GetRoute();
}
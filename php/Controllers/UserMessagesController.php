<?php

require_once("php/Controllers/BaseController.php");
require_once("php/Tools/UserMessagesHandler.php");

class UserMessagesController extends BaseController
{
    const USER_MESSAGES_ROUTE = "user_messages";

    public function __construct($bdd, $smarty)
    {
        parent::__construct($bdd, $smarty, null, self::USER_MESSAGES_ROUTE);
    }

    public function GetContent()
    {
        $userMessagesHandler = UserMessagesHandler::GetInstance();

        if (!$userMessagesHandler->IsEmpty())
        {
            $messages = $userMessagesHandler->GetMessages();
            $this->smarty->assign("UserMessagesList", $messages);
        }
        return $this->smarty->fetch("html/userMessages.html");
    }

    public static function GetRoute()
    {
        return self::USER_MESSAGES_ROUTE;
    }
}

?>
<?php

class UserMessagesHandler
{
    private static $_instance;
    
    private $messages = array();

    public static function GetInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new UserMessagesHandler;
        }

        return self::$_instance;
    }

    private function __construct()
    {

    }

    public function AddMessage($type, $message)
    {
        array_push($this->messages, array("MessageType" => $type, "MessageString" => $message));
    }

    public function GetMessages()
    {
        return $this->messages;
    }

    public function IsEmpty()
    {
        return count($this->messages) == 0;
    }
}

?>
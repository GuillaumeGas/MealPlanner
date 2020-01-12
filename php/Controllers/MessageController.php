<?php

class MessageController {
    public static function msg($message, $redirection, $smarty) {
        $smarty->assign("Message", $message);
        $smarty->assign("Redirection", $redirection);
        return $smarty->fetch("/html/message.html");
    }
}

?>
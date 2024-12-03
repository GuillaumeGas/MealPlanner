<?php
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=mealplanner;port=3306', 'root', '');
        $base = "mealplanner";
        $bdd->exec("SET NAMES UTF8");
    } catch( PDOException $Exception ) {
        // Note The Typecast To An Integer!
        echo $Exception->getMessage( ) ." (code : ". (int)$Exception->getCode( ).")";
    }

<?php
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=mealfinder;port=3308', 'guidono', '235');
        $base = "mealfinder";
        $bdd->exec("SET NAMES UTF8");
    } catch( PDOException $Exception ) {
        // Note The Typecast To An Integer!
        echo $Exception->getMessage( ) ." (code : ". (int)$Exception->getCode( ).")";
    }

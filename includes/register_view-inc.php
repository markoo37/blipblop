<?php

function check_registration_errors()
{
    if(isset($_SESSION["error_signup"])){
        $errors = $_SESSION["error_signup"];

        echo "<br>";

        foreach($errors as $error){
            echo '<p class="error-text">' . $error . '</p>';
            break;
        }

        unset($_SESSION["error_signup"]);
    }
}

function check_registration_valid(){
    if(isset($_SESSION["success_signup"])){

        echo '<p class="success-text">Sikeres regisztáció! Átirányítás a kezdőlapra...</p>';

        unset($_SESSION["success_signup"]);
    }
}

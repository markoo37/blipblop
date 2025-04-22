<?php
function check_login_errors()
{
    if(isset($_SESSION["error_login"])){
        $errors = $_SESSION["error_login"];

        echo "<br>";

        foreach($errors as $error){
            echo '<p class="error-text">' . $error . '</p>';
            break;
        }

        unset($_SESSION["error_login"]);
    }
}
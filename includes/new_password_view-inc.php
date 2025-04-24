<?php
function check_new_password_errors()
{
    if(isset($_SESSION["error_new_password"])){
        $errors = $_SESSION["error_new_password"];

        echo "<br>";

        foreach($errors as $error){
            echo '<p class="error-text">' . $error . '</p>';
            break;
        }

        unset($_SESSION["error_new_password"]);
    }
}

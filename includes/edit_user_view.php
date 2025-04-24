<?php
function check_admin_edit_errors()
{
    if(isset($_SESSION["error_modify"])){
        $errors = $_SESSION["error_modify"];

        echo "<br>";

        foreach($errors as $error){
            echo '<p class="error-text">' . $error . '</p>';
            break;
        }

        unset($_SESSION["error_modify"]);
    }
}
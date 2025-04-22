<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $password_again = htmlspecialchars($_POST['confirm_password']);

    try{
        require_once 'dbh-inc.php';
        require_once 'register_model-inc.php';
        require_once 'register_contr-inc.php';
        $conn = getConnection();
        //error handlers:

        echo "<h3>Registration Successful</h3>";
        echo "Username: " . $username . "<br>";

        $errors = [];

        if(is_input_empty($username, $password, $password_again)){
            $errors["empty_input"] = "Kérem töltse ki az összes mezőt!";
        }

        if (is_username_invalid($username)){
            $errors["invalid_username"] = "A megadott felhasználónév nem megfelelő!";
        }

        if (is_password_tooShort($password)){
            $errors["password_too_short"] = "A megadott jelszó túl rövid!";
        }

        if (passwords_dont_match($password, $password_again)){
            $errors["passwords_dont_match"] = "A két jelszó nem egyezik!";
        }

        require_once 'config_session-inc.php';

        if ($errors){
            $_SESSION['error_signup'] = $errors;
            header("Location: ../index.php?page=home");

            die();
        }

        registerUser($conn, $username, $password);
    }
    catch(PDOException $e){
        die("query failed: " . $e->getMessage());
    }
}
else{
    header("Location: ../index.php");
    die();
}

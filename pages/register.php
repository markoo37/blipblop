<?php
require_once 'includes/register_view-inc.php';
try{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        require_once 'includes/dbh-inc.php';
        require_once 'includes/register_model-inc.php';
        require_once 'includes/register_contr-inc.php';
        $conn = getConnection();
        // Collect form data
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $password_again = htmlspecialchars($_POST['password_again']);

        $errors = [];

        if(is_input_empty($username, $password, $password_again)){
            $errors["empty_input"] = "Kérem töltse ki az összes mezőt!";
        }

        if (is_username_invalid($username)){
            $errors["invalid_username"] = "A megadott felhasználónév nem megfelelő!";
        }

        if (is_username_taken($conn, $username)){
            $errors["taken_username"] = "A megadott felhasználónév már létezik!";
        }

        if (is_password_tooShort($password)){
            $errors["password_too_short"] = "A megadott jelszó túl rövid!";
        }

        if (passwords_dont_match($password, $password_again)){
            $errors["passwords_dont_match"] = "A két jelszó nem egyezik!";
        }

        //require_once 'includes/config_session-inc.php';

        if ($errors){
            $_SESSION['error_signup'] = $errors;
            header("Location: index.php?page=register");

            die();
        }

        registerUser($conn, $username, password_hash($password, PASSWORD_DEFAULT));
        $_SESSION['success_signup'] = true;

        $query_of_current_user_id = "SELECT a.user_id, ut.type_name FROM app_users a JOIN user_types ut ON a.type_id = ut.type_id WHERE username = :username";
        $stmt_userid = $conn->prepare($query_of_current_user_id);
        $stmt_userid->bindParam(':username', $username);
        $stmt_userid->execute();

        $userid = null;
        if ($row = $stmt_userid->fetch(PDO::FETCH_ASSOC)) {
            $userid = $row['USER_ID'];
            $user_type = $row['TYPE_NAME'];
            $_SESSION['user_id'] = $userid; // replace with real ID
            $_SESSION['username'] = $username;
            $_SESSION['user_type'] = $user_type;
        } else {
            echo "User not found.";
        }

        echo '<p class="success-text">Sikeres regisztáció! Átirányítás a kezdőlapra...</p>';
        echo "<script>
        setTimeout(function() {
            window.location.href = 'index.php?page=home';
        }, 1500);
        </script>";

        exit();
    }
}
catch(PDOException $e){
    echo "DB error: " . $e->getMessage();
}
?>

<div class="form-container">
    <div class="form-header">
        <h1>Regisztráció</h1>
        <p>Legyen blipblop tag és töltse fel a videóit!</p>
    </div>



    <form action="" method="post">
        <div class="form-group">
            <label for="username">Felhasználónév</label>
            <input type="text" id="username" name="username" class="form-control">
        </div>

        <div class="form-group">
            <label for="password">Jelszó</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>

        <div class="form-group">
            <label for="password_again">Jelszó megerősítése</label>
            <input type="password" id="password_again" name="password_again" class="form-control">
        </div>

        <button type="submit" class="btn" style="width: 100%;">Regisztráció</button>
    </form>

    <div class="form-footer">
        <p>Van már fiókod? <a href="index.php?page=login" class="page-link" data-page="login">Bejelentkezés</a></p>
        <?php check_registration_errors(); check_registration_valid();?>
    </div>
</div>
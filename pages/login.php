<?php
require_once 'includes/login_view-inc.php';
try{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        require_once 'includes/dbh-inc.php';
        require_once 'includes/login_model-inc.php';
        require_once 'includes/login_contr-inc.php';
        $conn = getConnection();
        // Collect form data
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        $errors = [];

        if(is_input_empty($username, $password)){
            $errors["empty_input"] = "Kérem töltse ki az összes mezőt!";
        }

        if (!does_user_exist($conn, $username)){
            $errors["user_doesnt_exist"] = "A megadott felhasználónév nincs még regisztrálva!";
        }
        //require_once 'includes/config_session-inc.php';

        $stmt = $conn->prepare("SELECT a.user_id, a.username, a.pword, ut.type_name FROM app_users a JOIN user_types ut ON a.type_id = ut.type_id WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!password_verify($password, $user['PWORD'])) {
            $errors["wrong_password"] = "A megadott jelszó hibás!";
        }

        if ($errors){
            $_SESSION['error_login'] = $errors;
            header("Location: /untitled/index.php?page=login");

            die();
        }

        $_SESSION['user_id'] = $user['USER_ID'];
        $_SESSION['username'] = $user['USERNAME'];
        $_SESSION['user_type'] = $user['TYPE_NAME'];

        echo '<p class="success-text">Sikeres bejelentkezés! Átirányítás a kezdőlapra...</p>';
        echo "<script>
        setTimeout(function() {
            window.location.href = '/untitled/index.php?page=home';
        }, 300);
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
        <h1>Bejelentkezés</h1>
        <p>Üdvözöljuk újra!</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">
        <div class="form-group">
            <label for="email">Felhasználónév</label>
            <input type="text" id="email" name="username" class="form-control">
        </div>

        <div class="form-group">
            <label for="password">Jelszó</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>

        <button type="submit" class="btn" style="width: 100%;">Bejelentkezés</button>
    </form>

    <div class="form-footer">
        <p>Nincs még fiókod? <a href="index.php?page=register" class="page-link" data-page="register">Regisztráció</a></p>
        <?php check_login_errors(); ?>
    </div>
</div>
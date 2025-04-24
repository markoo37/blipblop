<?php

require_once 'includes/dbh-inc.php';
require_once 'includes/edit_user_view.php';
$conn = getConnection();

$id_of_selected_user = $_GET['id'];

$sql = "SELECT a.username, a.pword, ut.type_name FROM app_users a JOIN user_types ut ON a.type_id = ut.type_id WHERE a.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $id_of_selected_user);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$sql_of_types = "SELECT type_id, type_name FROM user_types";
$stmt_of_types = $conn->prepare($sql_of_types);
$stmt_of_types->execute();
$types = $stmt_of_types->fetchAll(PDO::FETCH_ASSOC);

$update_username = false;
$update_password = false;
$update_usertype = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    require_once 'includes/register_contr-inc.php';
    require_once 'includes/register_model-inc.php';

    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $new_type = $_POST['type_name'];

    $errors = [];

    if (isset($_POST['update_username'])){

        if(empty($new_username)){
            $errors["username_empty_field"] = "Nincs megadva felhasználónév!";
        }

        if (is_username_invalid($new_username)){
            $errors["invalid_username"] = "Nem megfelelő felhasználónév!";
        }

        if(is_username_taken($conn, $new_username)){
            $errors["username_already_taken"] = "Már létezik ilyen felhasználónév!";
        }

        $update_username = true;
    }

    if (isset($_POST['update_password'])){

        if(empty($new_password)){
            $errors["password_empty_field"] = "Nincs megadva új jelszó!";
        }

        if (is_password_tooShort($new_password)){
            $errors["invalid_password"] = "Túl rövid jelszó!";
        }

        if (password_verify($new_password, $user['PWORD'])) {
            $errors["samepass"] = "A megadott jelszó egyezik az eddigi jelszóval!";
        }

        $update_password = true;
    }

    if (isset($_POST['update_type'])){


        if ($user["TYPE_NAME"] == $new_type) {
            $errors["same_role"] = "A felhasználónak eddig is ez volt a szerepköre!";
        }

        $update_usertype = true;
    }

    if ($errors){
        $_SESSION['error_modify'] = $errors;
        header("Location: /blipblop/index.php?page=edituser&id=$id_of_selected_user");

        die();
    }

    if($update_username){
        $sql = "UPDATE app_users SET username = :username WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $new_username);
        $stmt->bindParam(':user_id', $id_of_selected_user);
        $stmt->execute();
    }
    if($update_password){
        $sql = "UPDATE app_users SET pword = :password WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashed);
        $stmt->bindParam(':user_id', $id_of_selected_user);
        $stmt->execute();
    }
    if($update_usertype){
        $sql = "UPDATE app_users SET type_id = :type_id WHERE user_id = :user_id";
        $type_id = 1;
        if($new_type == 'admin'){
            $type_id = 0;
        }
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':type_id', $type_id);
        $stmt->bindParam(':user_id', $id_of_selected_user);
        $stmt->execute();
    }

    echo '<p class="success-text">Sikeres módosítás! Vissza a kezdőlapra...</p>';
    echo "<script>
        setTimeout(function() {
            window.location.href = '/untitled/index.php?page=home';
        }, 1500);
        </script>";

    exit();
}


?>


<div class="container">
    <h1>Felhasználó szerkesztése: <?= htmlspecialchars($user['USERNAME']) ?></h1>

    <form method="POST" action="">
        <div class="form-group">
            <label>Új felhasználónév</label>
            <input type="text" name="new_username" value="">
            <label>Új jelszó</label>
            <input type="password" name="new_password" value="">
        </div>

        <div class="form-group">
            <label>Új fióktípus</label>
            <select name="type_name">
                <?php foreach ($types as $type): ?>
                    <option
                        value="<?= htmlspecialchars($type['TYPE_NAME']) ?>"
                        <?= $type['TYPE_NAME'] == $user['TYPE_NAME'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($type['TYPE_NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
            <label for="update_username">
                Felhasználónév módosítása
                <input type="checkbox" id="update_username" name="update_username">
            </label>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
            <label for="update_password">
                Jelszó módosítása
                <input type="checkbox" id="update_password" name="update_password">
            </label>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
            <label for="update_type">
                Fióktípus módosítása
                <input type="checkbox" id="update_type" name="update_type">
            </label>
        </div>



        <button type="submit" class="btn">Mentés</button>
        <a href="index.php?page=home" class="btn" style="background:#ccc; color:#333; margin-left:1rem;">
            Mégse
        </a>
    </form>
    <?php check_admin_edit_errors();?>
</div>
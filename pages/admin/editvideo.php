<?php

require_once 'includes/dbh-inc.php';
require_once 'includes/edit_video_view.php';
$conn = getConnection();

$id_of_selected_video = $_GET['id'];

$sql = "SELECT v.title, a.username FROM videos v JOIN app_users a ON v.uploader_user_id = a.user_id WHERE v.video_id = :video_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':video_id', $id_of_selected_video);
$stmt->execute();
$video = $stmt->fetch(PDO::FETCH_ASSOC);

$comment_sql = "SELECT c.comment_id, a.username, TO_CHAR(c.comment_time, 'YYYY-MM-DD HH24:MI:SS') AS comment_time, c.comment_text FROM comments c JOIN app_users a ON a.user_id = c.user_id WHERE c.video_id = :video_id";
$stmt_comment = $conn->prepare($comment_sql);
$stmt_comment->bindParam(':video_id', $id_of_selected_video);
$stmt_comment->execute();
$comments = $stmt_comment->fetchAll(PDO::FETCH_ASSOC);

$update_title = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_title'])){

    require_once 'includes/register_contr-inc.php';
    require_once 'includes/register_model-inc.php';

    $new_title = $_POST['new_title'];

    $errors = [];

    if (isset($_POST['update_title'])){

        if(empty($new_title)){
            $errors["title_empty_field"] = "Nincs megadva cím!";
        }

        if (strlen($new_title) < 4){
            $errors["title_too_short"] = "Nem megfelelő cím!";
        }

        $update_title = true;
    }

    if(!$update_title){
        $errors['tick_not_checked'] = "A módosításhoz be kell pipálni!";
    }

    if ($errors){
        $_SESSION['error_modify_video'] = $errors;
        header("Location: /blipblop/index.php?page=editvideo&id=$id_of_selected_video");

        die();
    }

    if($update_title){
        $sql = "UPDATE videos SET title = :title WHERE video_id = :video_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $new_title);
        $stmt->bindParam(':video_id', $id_of_selected_video);
        $stmt->execute();
    }


    echo '<p class="success-text">Sikeres módosítás! Vissza a kezdőlapra...</p>';
    echo "<script>
        setTimeout(function() {
            window.location.href = '/blipblop/index.php?page=home';
        }, 1500);
        </script>";

    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['type'])){
    $comment_id = $_POST['id'];
    $stmt_delcom = $conn->prepare('DELETE FROM comments WHERE comment_id = :id');
    $stmt_delcom->bindParam(':id', $comment_id);
    $stmt_delcom->execute();

    header("Location: /blipblop/index.php?page=editvideo&id=$id_of_selected_video");
}


?>


<div class="container">
    <h1>Videó szerkesztése: <?= htmlspecialchars($video['TITLE']) ?></h1>
    <h2>Csatorna: <?= htmlspecialchars($video['USERNAME']) ?></h2>
    <form method="POST" action="">
        <div class="form-group">
            <label>Új cím</label>
            <input type="text" name="new_title" value="">
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
            <label for="update_title">
                Cím módosítása
                <input type="checkbox" id="update_title" name="update_title">
            </label>
        </div>

        <button type="submit" class="btn">Mentés</button>
        <a href="index.php?page=home" class="btn" style="background:#ccc; color:#333; margin-left:1rem;">
            Mégse
        </a>
    </form>
    <?php check_admin_edit_errors();?>


    <section>
        <h2>Kommentek</h2>
        <table>
            <thead>
            <tr>
                <th>COMMENTID</th>
                <th>Feltöltő</th>
                <th>Komment</th>
                <th>Feltöltve</th>
                <th>Műveletek</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($comments as $com): ?>
                <?php
                $rawTimestamp = $com['COMMENT_TIME'];// pl. "2025-04-22 16:38:57.123456"
                $datetime = new DateTime($rawTimestamp);
                ?>
                <tr>
                    <td><?= htmlspecialchars($com['COMMENT_ID']) ?></td>
                    <td><?= htmlspecialchars($com['USERNAME']) ?></td>
                    <td><?= htmlspecialchars($com['COMMENT_TEXT']) ?></td>
                    <td><?= $datetime->format('Y-m-d h:m'); ?></td>
                    <td class="actions">
                        <!--<a href="index.php?page=editvideo&id=<?php /*= $com['VIDEO_ID'] */?>" class="btn">Szerkesztés</a>-->
                        <form method="POST" onsubmit="return confirm('Biztos törölni akarod?');">
                            <input type="hidden" name="type" value="comment">
                            <input type="hidden" name="id" value="<?= $com['COMMENT_ID'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn">Törlés</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
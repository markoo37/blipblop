<?php
echo '<h2>Fejlesztés alatt</h2>';

require_once 'includes/dbh-inc.php';
$conn = getConnection();

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT category_id, category_name FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = $_POST['video_title'];
    $category_id = $_POST['category_id'];

    $stmt_upload_video = $conn->prepare("INSERT INTO videos (uploader_user_id, title, category_id, upload_time, views) VALUES (:user_id, :title, :category_id, SYSTIMESTAMP, 0)");
    $stmt_upload_video->bindParam(':user_id', $user_id);
    $stmt_upload_video->bindParam(':title', $title);
    $stmt_upload_video->bindParam(':category_id', $category_id);
    $stmt_upload_video->execute();

    header("Location: /blipblop/index.php?page=account");
    exit();

}

?>

<div class="container">
    <h1>Videó Feltöltése</h1>

    <form method="POST" action="">
        <div class="form-group">
            <label>Cím</label>
            <input type="text" name="video_title" value="" required>
        </div>

        <div class="form-group">
            <label>Kategória</label>
            <select name="category_id">
                <?php foreach ($categories as $cat): ?>
                    <option
                            value="<?= htmlspecialchars($cat['CATEGORY_ID']) ?>">
                        <?= htmlspecialchars($cat['CATEGORY_NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn">Mentés</button>
        <a href="index.php?page=home" class="btn" style="background:#ccc; color:#333; margin-left:1rem;">
            Mégse
        </a>
    </form>
</div>

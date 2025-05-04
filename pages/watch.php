<?php

require_once 'includes/dbh-inc.php';
$conn = getConnection();

$video_id = $_GET['id'];

// 1. Videó metaadatok lekérdezése
$video_sql = "SELECT a.username, v.title, TO_CHAR(v.upload_time, 'YYYY-MM-DD HH24:MI:SS') AS upload_time, v.views, v.uploader_user_id, cat.category_name
              FROM videos v
              JOIN app_users a ON v.uploader_user_id = a.user_id
              JOIN categories cat ON v.category_id = cat.category_id
              WHERE video_id = :video_id";
$stmt_video = $conn->prepare($video_sql);
$stmt_video->bindParam(':video_id', $video_id);
$stmt_video->execute();
$video = $stmt_video->fetch(PDO::FETCH_ASSOC);

$isOwner = isset($_SESSION['user_id'])
    && $_SESSION['user_id'] == $video['UPLOADER_USER_ID'];

// 2. Kommentek lekérdezése
$comments_sql = "SELECT a.username, c.comment_text, TO_CHAR(c.comment_time, 'YYYY-MM-DD HH24:MI:SS') AS comment_time
                 FROM comments c
                 JOIN app_users a ON c.user_id = a.user_id
                 WHERE c.video_id = :video_id
                 ORDER BY c.comment_time ASC";
$stmt_comments = $conn->prepare($comments_sql);
$stmt_comments->bindParam(':video_id', $video_id);
$stmt_comments->execute();
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

// 3. Megtekintés számláló növelése
$updateViews = $conn->prepare("UPDATE videos SET views = views + 1 WHERE video_id = :video_id");
$updateViews->execute([':video_id' => $video_id]);

// 4. Lájkok száma és állapot lekérése
$likesCountSql = "SELECT COUNT(*) AS cnt FROM likes WHERE video_id = :video_id";
$stmt_likes = $conn->prepare($likesCountSql);
$stmt_likes->bindParam(':video_id', $video_id);
$stmt_likes->execute();
$likesCount = (int) $stmt_likes->fetchColumn();

$userLiked = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userLikedSql = "SELECT COUNT(*) FROM likes WHERE video_id = :video_id AND user_id = :user_id";
    $stmt_userLiked = $conn->prepare($userLikedSql);
    $stmt_userLiked->bindParam(':video_id', $video_id);
    $stmt_userLiked->bindParam(':user_id', $userId);
    $stmt_userLiked->execute();
    $userLiked = ((int) $stmt_userLiked->fetchColumn() > 0);
}

// 5. POST feldolgozás: like/unlike vagy komment küldés
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_POST['delete_video']) && $isOwner){
        $del = $conn->prepare("DELETE FROM videos WHERE video_id = :video_id");
        $del->bindParam(':video_id', $video_id, PDO::PARAM_INT);
        $del->execute();
        header("Location: /blipblop/index.php?page=home");
        exit();
    }

    if (isset($_POST['like_action']) && isset($_SESSION['user_id'])) {
        // Lájk vagy unlik
        $userId = $_SESSION['user_id'];
        if ($_POST['like_action'] === 'like' && !$userLiked) {
            $sql = "INSERT INTO likes (user_id, video_id, like_time) VALUES (:user_id, :video_id, SYSTIMESTAMP)";
            $st = $conn->prepare($sql);
            $st->bindParam(':user_id', $userId);
            $st->bindParam(':video_id', $video_id);
            $st->execute();
        } elseif ($_POST['like_action'] === 'unlike' && $userLiked) {
            $sql = "DELETE FROM likes WHERE user_id = :user_id AND video_id = :video_id";
            $st = $conn->prepare($sql);
            $st->bindParam(':user_id', $userId);
            $st->bindParam(':video_id', $video_id);
            $st->execute();
        }
    } elseif (isset($_POST['comment_content']) && isset($_SESSION['user_id'])) {
        // Új komment beszúrása
        $comment_text = $_POST['comment_content'];
        $comment_upload_sql = "
        INSERT INTO comments (user_id, video_id, comment_time, comment_text)
        VALUES (:user_id, :video_id, SYSTIMESTAMP, :comment_text)";
        $uploadcomment_stmt = $conn->prepare($comment_upload_sql);
        $uploadcomment_stmt->bindParam(':user_id', $userId);
        $uploadcomment_stmt->bindParam(':video_id', $video_id);
        $uploadcomment_stmt->bindParam(':comment_text', $comment_text);
        $uploadcomment_stmt->execute();
    }
    // Oldal újratöltése friss adatokkal
    header("Location: /blipblop/index.php?page=watch&id=$video_id#meta");
    exit();
}

// Dátum formázása
$rts = $video['UPLOAD_TIME'];
$datetime = new DateTime($rts);
?>

<div class="container">
    <div class="video-player">
        <h1><?= htmlspecialchars($video['TITLE']) ?></h1>
        <video controls width="100%">
            <!-- Video forrása ide kerül majd -->
        </video>
        <p id="meta" class="meta">
            Feltöltő: <?= htmlspecialchars($video['USERNAME']) ?> |
            <?= $datetime->format('l, j. F Y H:i'); ?> |
            Megtekintések: <?= $video['VIEWS']  ?> |
            Kategória: <?= htmlspecialchars($video['CATEGORY_NAME']) ?>
        </p>

        <!-- Lájk gomb és számláló -->
        <div id="video-likes" class="video-likes">
            <form method="POST" style="display:inline;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($userLiked): ?>
                        <button name="like_action" value="unlike" class="btn btn-secondary">
                            👍
                        </button>
                    <?php else: ?>
                        <button name="like_action" value="like" class="btn btn-empty">
                            👍
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn btn-primary">Jelentkezz be a lájkoláshoz</a>
                <?php endif; ?>
                <span class="like-count"><?= $likesCount ?> lájk</span>
            </form>
            <?php if ($isOwner): // csak a tulajdonos láthatja ?>
                <form
                        method="POST"
                        onsubmit="return confirm('Biztosan törölni akarod ezt a videót?');"
                        style="display:inline; margin-left:0.5em;"
                >
                    <button
                            type="submit"
                            name="delete_video"
                            class="btn btn-outline-danger"
                            title="Videó törlése"
                    >
                        🗑️
                    </button>
                </form>
            <?php endif; ?>
        </div>

    </div>

    <section class="comments">
        <h2>Hozzászólások (<?= count($comments) ?>)</h2>

        <?php if (count($comments) === 0): ?>
            <p>Még nincs hozzászólás ehhez a videóhoz.</p>
        <?php else: ?>
            <?php foreach ($comments as $c): ?>
                <?php $dt = new DateTime($c['COMMENT_TIME']); ?>
                <div class="comment">
                    <strong><?= htmlspecialchars($c['USERNAME']) ?></strong>
                    <span class="timestamp"><?= $dt->format('l, j. F Y H:i'); ?></span>
                    <p><?= htmlspecialchars($c['COMMENT_TEXT']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" class="comment-form">
                <div class="form-group">
                    <label for="comment_content">Új hozzászólás:</label>
                    <textarea id="comment_content" name="comment_content" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn">Küldés</button>
            </form>
        <?php else: ?>
            <p><a href="index.php?page=login">Jelentkezz be</a> a hozzászóláshoz.</p>
        <?php endif; ?>
    </section>
</div>

<style>
    .video-likes { margin: 1em 0; }
    .video-likes .btn { margin-right: 0.5em; }
    .like-count { font-weight: bold; }

    .btn-empty{
        background: white;
        border-color: #c82333;
        color: #333333;
    }
</style>

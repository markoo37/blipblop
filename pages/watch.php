<?php

require_once 'includes/dbh-inc.php';
$conn = getConnection();

$video_id = $_GET['id'];

$video_sql = "SELECT a.username, v.title, TO_CHAR(v.upload_time, 'YYYY-MM-DD HH24:MI:SS') AS upload_time, v.views, cat.category_name FROM videos v JOIN app_users a ON v.uploader_user_id = a.user_id JOIN categories cat ON v.category_id = cat.category_id WHERE video_id = :video_id";
$stmt_video = $conn->prepare($video_sql);
$stmt_video->bindParam(':video_id', $video_id);
$stmt_video->execute();
$video = $stmt_video->fetch(PDO::FETCH_ASSOC);

$comments_sql = "SELECT a.username, c.comment_text, TO_CHAR(c.comment_time, 'YYYY-MM-DD HH24:MI:SS') AS comment_time FROM comments c JOIN app_users a ON c.user_id = a.user_id WHERE c.video_id = :video_id";
$stmt_comments = $conn->prepare($comments_sql);
$stmt_comments->bindParam(':video_id', $video_id);
$stmt_comments->execute();
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

$updateViews = $conn->prepare("UPDATE videos SET views = views + 1 WHERE video_id = :vid");
$updateViews->execute([':vid' => $video_id]);

$rts = $video['UPLOAD_TIME'];// pl. "2025-04-22 16:38:57.123456"
$datetime = new DateTime($rts);

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $comment_upload_user_id = $_SESSION['user_id'];
    $comment_text = $_POST['comment_content'];

    $comment_upload_sql = "
    INSERT INTO comments 
        ( 
        user_id,
        video_id,
        comment_time,
        comment_text
    ) VALUES (
        :user_id,
        :video_id,
        SYSTIMESTAMP,
        :comment_text
    )";
    $uploadcomment_stmt = $conn->prepare($comment_upload_sql);
    $uploadcomment_stmt->bindParam(':user_id', $comment_upload_user_id);
    $uploadcomment_stmt->bindParam(':video_id', $video_id);
    $uploadcomment_stmt->bindParam(':comment_text', $comment_text);
    $uploadcomment_stmt->execute();

    header("Location: /blipblop/index.php?page=watch&id=$video_id");
    exit();

}

?>

<div class="container">
    <div class="video-player">
        <h1><?= htmlspecialchars($video['TITLE']) ?></h1>
        <video controls width="100%">

        </video>
        <p class="meta">Feltöltő: <?= htmlspecialchars($video['USERNAME']) ?> | <?= $datetime->format('l, j. F Y H:i'); ?> | Megtekintések: <?= $video['VIEWS'] + 1 ?> | Kategória: <?= $video['CATEGORY_NAME'] ?></p>
    </div>

    <section class="comments">
        <h2>Hozzászólások (<?= count($comments) ?>)</h2>

        <?php if (count($comments) === 0): ?>
            <p>Még nincsen hozzászólás ehhez a videóhoz.</p>
        <?php else: ?>
            <?php foreach ($comments as $c): ?>
                <?php
                $rawTimestamp = $c['COMMENT_TIME'];// pl. "2025-04-22 16:38:57.123456"
                $datetime = new DateTime($rawTimestamp);
                ?>
                <div class="comment">
                    <strong><?= htmlspecialchars($c['USERNAME']) ?></strong>
                    <span class="timestamp"><?= $datetime->format('l, j. F Y H:i'); ?></span>
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